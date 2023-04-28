<?php
/**
 * Manages application migrations.
 */

namespace dezero\console\controllers;

use dezero\db\Migration;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Str;
use yii\base\InvalidParamException;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\db\Query;
use yii\helpers\Console;
use Dz;
use Yii;

class MigrateController extends BaseMigrateController
{
    /**
     * @var string Module ID (optional parameter)
     */
    public $module_id;


    /**
     * Array with migrations information
     */
    public $vec_migrations_info = [];


    /**
     * {@inheritdoc}
     */
    private $_migrationNameLimit;


    /**
     * {@inheritdoc}
     */
    public function options($actionID)
    {
        return array_merge(
            parent::options($actionID),
            ['module_id'],                   // module ID specific param
        );
    }


    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Module specific?
        if ( $this->module_id )
        {
            $core_module = str_replace("core_", "", $this->module_id );
            $dzlab_module = str_replace("dzlab_", "", $this->module_id );
            if ( ! Yii::$app->hasModule($this->module_id) && ! Yii::$app->hasModule($core_module) && ! Yii::$app->hasModule($dzlab_module) )
            {
                throw new InvalidParamException('Module `'. $this->module_id .'` does not exist.');
            }

            // Add module path
            $this->migrationPath = [$this->getMigrationPathByModule($this->module_id)];
        }

        return parent::beforeAction($action);
    }


    /**
     * Returns the migrations that are not applied.
     * @return array list of new migrations
     */
    protected function getNewMigrations()
    {
        // Add the migrations path for all the modules
        $vec_modules = $this->getModulesList();
        $vec_modules_path = [];
        if ( !empty($vec_modules) )
        {
            foreach ( $vec_modules as $module_id => $module_namespace )
            {
                if ( $module_id !== 'core' && $module_id !== 'gii' )
                {
                    // echo $module_id .' - '. $this->getMigrationPathByModule($module_id) ."\n";
                    $module_migration_path = Yii::getAlias($this->getMigrationPathByModule($module_id));
                    $this->migrationPath[] = $module_migration_path;

                    // Save "module_migration_path" and "module_id" relation
                    $vec_modules_path[$module_migration_path] = $module_id;
                }
            }
        }

        // return parent::getNewMigrations();

        $vec_applied = [];
        foreach ( $this->getMigrationHistory(null) as $class => $time )
        {
            $vec_applied[trim($class, '\\')] = true;
        }

        $migrationPaths = [];
        if ( is_array($this->migrationPath) )
        {
            foreach ( $this->migrationPath as $path )
            {
                $migrationPaths[] = [$path, ''];
            }
        }
        elseif ( !empty($this->migrationPath) )
        {
            $migrationPaths[] = [$this->migrationPath, ''];
        }

        foreach ( $this->migrationNamespaces as $namespace )
        {
            $migrationPaths[] = [$this->getNamespacePath($namespace), $namespace];
        }

        $vec_migrations = [];
        foreach ( $migrationPaths as $item )
        {
            list($migrationPath, $namespace) = $item;
            if ( !file_exists($migrationPath) )
            {
                continue;
            }
            $handle = opendir($migrationPath);
            while ( ( $file = readdir($handle) ) !== false )
            {
                if ( $file === '.' || $file === '..' )
                {
                    continue;
                }
                $path = $migrationPath . DIRECTORY_SEPARATOR . $file;
                if ( preg_match('/^(m(\d{6}_?\d{6})\D.*?)\.php$/is', $file, $matches) && is_file($path) )
                {
                    $class = $matches[1];
                    if ( !empty($namespace) )
                    {
                        $class = $namespace . '\\' . $class;
                    }
                    $time = str_replace('_', '', $matches[2]);
                    if ( !isset($vec_applied[$class]) )
                    {
                        $vec_migrations[$time . '\\' . $class] = $class;

                        // Save module
                        $module_id = isset($vec_modules_path[$migrationPath]) ? $vec_modules_path[$migrationPath] : 'core';
                        $this->vec_migrations_info[$class] = $module_id;
                    }
                }
            }
            closedir($handle);
        }

        ksort($vec_migrations);

        return array_values($vec_migrations);
    }


    /**
     * Creates the migration history table.
     */
    protected function createMigrationHistoryTable()
    {
        $tableName = $this->db->schema->getRawTableName($this->migrationTable);
        $this->stdout("Creating migration history table \"$tableName\"...", Console::FG_YELLOW);

        // Creates a new migration instance.
        $migration = $this->createMigration(Migration::class);

        // CREATE TABLE
        $this->db->createCommand()->createTable($this->migrationTable, [
            'migration_id'  => $migration->primaryKey(),
            'name'          => $migration->string(static::MAX_NAME_LENGTH)->notNull(),
            'apply_date'    => $migration->date()->notNull(),
            'module'        => $migration->string(64),
            'uuid'          => $migration->uuid(),
        ])->execute();

        // ADD INDEXES
        $this->db->createCommand()->createIndex($this->db->getIndexName($this->migrationTable, ['module'], false), $this->migrationTable, ['module'], false);
        $this->db->createCommand()->createIndex($this->db->getIndexName($this->migrationTable, ['uuid'], false), $this->migrationTable, ['uuid'], false);

        // INSERT DATA
        $this->db->createCommand()->insert($this->migrationTable, [
            'name'          => self::BASE_MIGRATION,
            'apply_date'    => time(),
            'module'        => 'core',
            'uuid'          => Str::UUID()
        ])->execute();

        $this->stdout("Done.\n", Console::FG_GREEN);
    }


    /**
     * {@inheritdoc}
     */
    protected function getMigrationHistory($limit)
    {
        // Create migration table, if it does not exist
        if ( $this->db->schema->getTableSchema($this->migrationTable, true) === null )
        {
            $this->createMigrationHistoryTable();
        }

        $query = (new Query())
            ->select(['name', 'apply_date'])
            ->from($this->migrationTable)
            ->orderBy(['apply_date' => SORT_DESC, 'name' => SORT_DESC]);

        if ( empty($this->migrationNamespaces) )
        {
            $query->limit($limit);
            $rows = $query->all($this->db);
            $history = ArrayHelper::map($rows, 'name', 'apply_date');
            unset($history[self::BASE_MIGRATION]);
            return $history;
        }

        $rows = $query->all($this->db);

        $history = [];
        foreach ( $rows as $key => $row )
        {
            if ( $row['name'] === self::BASE_MIGRATION )
            {
                continue;
            }
            if ( preg_match('/m?(\d{6}_?\d{6})(\D.*)?$/is', $row['name'], $matches) )
            {
                $time = str_replace('_', '', $matches[1]);
                $row['canonicalVersion'] = $time;
            }
            else
            {
                $row['canonicalVersion'] = $row['name'];
            }
            $row['apply_date'] = (int) $row['apply_date'];
            $history[] = $row;
        }

        usort($history, function ($a, $b) {
            if ( $a['apply_date'] === $b['apply_date'] )
            {
                if ( ($compareResult = strcasecmp($b['canonicalVersion'], $a['canonicalVersion'])) !== 0 )
                {
                    return $compareResult;
                }

                return strcasecmp($b['name'], $a['name']);
            }

            return ($a['apply_date'] > $b['apply_date']) ? -1 : +1;
        });

        $history = array_slice($history, 0, $limit);

        $history = ArrayHelper::map($history, 'name', 'apply_date');

        return $history;
    }


    /**
     * {@inheritdoc}
     */
    protected function addMigrationHistory($name)
    {
        $module_id = isset($this->vec_migrations_info[$name]) ? $this->vec_migrations_info[$name] : null;
        $this->db->createCommand()->insert($this->migrationTable, [
            'name'          => $name,
            'apply_date'    => time(),
            'module'        => $module_id,
            'uuid'          => Str::UUID()
        ])->execute();
    }


    /**
     * {@inheritdoc}
     */
    protected function removeMigrationHistory($name)
    {
        $this->db->createCommand()->delete($this->migrationTable, [
            'name' => $name,
        ])->execute();
    }


    /**
     * {@inheritdoc}
     * @since 2.0.13
     */
    protected function getMigrationNameLimit()
    {
        if ( $this->_migrationNameLimit !== null )
        {
            return $this->_migrationNameLimit;
        }

        $tableSchema = $this->db->schema ? $this->db->schema->getTableSchema($this->migrationTable, true) : null;
        if ( $tableSchema !== null )
        {
            return $this->_migrationNameLimit = $tableSchema->columns['name']->size;
        }

        return static::MAX_NAME_LENGTH;
    }


    /**
     * Get migration path given a module
     */
    protected function getMigrationPathByModule(string $module_id) : string
    {
        // Core global path on {$this->migrationPath}
        if ( $module_id === 'core' )
        {
            return $this->migrationPath[0];
        }

        // Core modules path on "core.src.modules"
        if ( preg_match("/^core\_/", $module_id) )
        {
            $module_id = str_replace("core_", "", $module_id);
            return "@dezero/modules/{$module_id}/migrations";
        }

        // Dz contrib modules path on "vendor.dezero"
        else if ( preg_match("/^dzlab\_/", $module_id) )
        {
            $module_id = str_replace("dzlab_", "", $module_id);
            return "@vendor/dezero/{$module_id}/src/migrations";
        }

        // App modules path on "app.modules"
        return "@app/modules/{$module_id}/migrations";
    }


    /**
     * Return Yii modules and Dezero Core & Contrib modules to check migrations
     */
    protected function getModulesList() : array
    {
        // Core modules
        $vec_modules = ['core' => 'core'];
        $vec_core_modules = Dz::getCoreModules();
        if ( !empty($vec_core_modules) )
        {
            foreach ( $vec_core_modules as $module_id => $que_module )
            {
                if ( Yii::$app->hasModule($module_id) )
                {
                    $vec_modules['core_'. $module_id] = $que_module;
                }
            }
        }

        // Contrib LAB modules
        $vec_dzlab_modules = Dz::getContribModules();
        if ( !empty($vec_dzlab_modules) )
        {
            foreach ( $vec_dzlab_modules as $module_id => $que_module )
            {
                if ( Yii::$app->hasModule($module_id) )
                {
                    $vec_modules['dzlab_'. $module_id] = $que_module;
                }
            }
        }

        return ArrayHelper::merge($vec_modules, Dz::getModules());
    }
}
