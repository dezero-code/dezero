<?php
/**
 * Database Schema class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\db\mysql;

use dezero\base\File;
use dezero\db\mysql\ColumnSchema;
use dezero\db\mysql\ColumnSchemaBuilder;
use dezero\db\mysql\QueryBuilder;
use dezero\helpers\FileHelper;
use dezero\helpers\StringHelper;
use Yii;

/**
 * Schema is the class for retrieving metadata from a MySQL database (version 4.1.x and 5.x).
 */
class Schema extends \yii\db\mysql\Schema
{
    // Special column data types
    public const TYPE_TINYTEXT = 'tinytext';
    public const TYPE_MEDIUMTEXT = 'mediumtext';
    public const TYPE_LONGTEXT = 'longtext';
    public const TYPE_ENUM = 'enum';
    public const TYPE_DATE_RAW = 'date';


    /**
     * @inheritdoc
     */
    public $columnSchemaClass = ColumnSchema::class;


    /**
     * @var int The maximum length that objects' names can be.
     */
    public $maxObjectNameLength = 64;


    /**
     * @var string|null The path to the temporary my.cnf file used for backups and restoration.
     */
    public $tempMyCnfPath = null;


    /**
     * @inheritdoc
     */
    public function init() : void
    {
        parent::init();

        $this->typeMap['tinytext'] = self::TYPE_TINYTEXT;
        $this->typeMap['mediumtext'] = self::TYPE_MEDIUMTEXT;
        $this->typeMap['longtext'] = self::TYPE_LONGTEXT;
        $this->typeMap['enum'] = self::TYPE_ENUM;

        // Change DATE to INTEGER data <--- not needed, problem with validations
        // $this->typeMap['date'] = self::TYPE_INTEGER;
    }


    /**
     * Creates a query builder for the database.
     *
     * This method may be overridden by child classes to create a DBMS-specific query builder.
     *
     * @return QueryBuilder query builder instance
     */
    public function createQueryBuilder() : QueryBuilder
    {
        return new QueryBuilder($this->db, [
            'separator' => "\n",
        ]);
    }


    /**
     * Create a column schema builder instance giving the type and value precision.
     *
     * This method may be overridden by child classes to create a DBMS-specific column schema builder.
     *
     * @param string $type type of the column. See [[ColumnSchemaBuilder::$type]].
     * @param int|string|array $length length or precision of the column. See [[ColumnSchemaBuilder::$length]].
     * @return ColumnSchemaBuilder column schema builder instance
     */
    public function createColumnSchemaBuilder($type, $length = null) : ColumnSchemaBuilder
    {
        return new ColumnSchemaBuilder($type, $length, $this->db);
    }


    /**
     * Builds a SQL statement for dropping a DB table if it exists.
     *
     * @param string $table The table to be dropped. The name will be properly quoted by the method.
     * @return string The SQL statement for dropping a DB table.
     */
    public function dropTableIfExists(string $table) : string
    {
        return 'DROP TABLE IF EXISTS ' . $this->db->quoteTableName($table);
    }


    /*
    |--------------------------------------------------------------------------
    | BACKUP METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the default backup command to execute.
     *
     * @return string The command to execute
     */
    public function getDefaultBackupCommand() : string
    {
        $default_args =
            ' --defaults-extra-file="' . $this->createDumpConfigFile() . '"' .
            ' --add-drop-table' .
            ' --comments' .
            ' --create-options' .
            ' --dump-date' .
            ' --no-autocommit' .
            ' --routines' .
            ' --default-character-set=' . Yii::$app->db->charset .
            ' --set-charset' .
            ' --triggers' .
            ' --no-tablespaces';

        $vec_ignore_table_args = [];
        foreach ( Yii::$app->db->getIgnoredBackupTables() as $table )
        {
            $vec_ignore_table_args[] = "--ignore-table={database}.{$table}";
        }

        // DEZERO - Custom backup command path
        $mysqldump_command_path = 'mysqldump';
        if ( isset(Yii::$app->params['backup_command']) )
        {
            $mysqldump_command_path = Yii::$app->params['backup_command'];
        }

        $schema_dump = $mysqldump_command_path .
            $default_args .
            ' --single-transaction' .
            ' --no-data' .
            ' --result-file="{file}"' .
            ' {database}';

        $data_dump = $mysqldump_command_path .
            $default_args .
            ' --no-create-info' .
            ' ' . implode(' ', $vec_ignore_table_args) .
            ' {database}';

        return "{$schema_dump} && {$data_dump} >> {file}";
    }


    /**
     * Delete temporary my.cnf file based on the DB config settings.
     */
    public function deleteDumpConfigFile() : bool
    {
        $config_file_path = Yii::getAlias('@privateTmp') . DIRECTORY_SEPARATOR . 'my.cnf';
        $config_file = File::load($config_file_path);
        if ( $config_file && $config_file->exists() )
        {
            return $config_file->delete();
        }

        return false;
    }


    /**
     * Creates a temporary my.cnf file based on the DB config settings.
     *
     * @return string The path to the my.cnf file
     */
    private function createDumpConfigFile() : string
    {
        // Get database configuration
        $vec_config = Yii::$app->config->getDb();
        $contents = '[client]' . PHP_EOL .
            'user=' . $vec_config['username'] . PHP_EOL .
            'password="' . addslashes($vec_config['password']) . '"' . PHP_EOL .
            'host=' . $vec_config['server'] . PHP_EOL .
            'port=' . $vec_config['port'];

        if ( isset($vec_config['unix_socket']) )
        {
            $contents .= PHP_EOL . 'socket=' . $vec_config['unix_socket'];
        }

        // Create a TEMP config file
        $config_file_path = Yii::getAlias('@privateTmp') . DIRECTORY_SEPARATOR . 'my.cnf';
        $config_file = File::load($config_file_path);
        if ( ! $config_file->exists() )
        {
            $config_file->createEmptyFile();
        }
        $config_file->write($contents);

        // Avoid a “world-writable config file 'my.cnf' is ignored” warning
        $config_file->setPermissions(644);
        // chmod($filePath, 0644);

        return $config_file_path;
    }
}
