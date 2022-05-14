<?php
/**
 * Manages application migrations.
 */

namespace dezero\console\controllers;

use dezero\db\Migration;
use dezero\helpers\Str;
use yii\console\controllers\MigrateController as BaseMigrateController;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Console;

class MigrateController extends BaseMigrateController
{
    private $_migrationNameLimit;

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
        $this->db->createCommand()->insert($this->migrationTable, [
            'name' => $name,
            'apply_date' => time(),
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
}
