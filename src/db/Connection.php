<?php
/**
 * Database Connection class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\db;

use dezero\base\File;
use dezero\db\mysql\QueryBuilder as MysqlQueryBuilder;
use dezero\db\mysql\Schema as MysqlSchema;
use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use dezero\helpers\Transliteration;
use mikehaertl\shellcommand\Command as ShellCommand;
use yii\base\Exception;
use Yii;

/**
 * Connection represents a connection to a database via [PDO](https://www.php.net/manual/en/book.pdo.php).
 */
class Connection extends \yii\db\Connection
{
    /**
     * @var string the charset used for database connection
     */
    public $collation;


    /**
     * @var array Ignored tables on backups
     */
    public $backupIgnoredTables;


    /**
     * Ensures that an object name is within the schema's limit.
     *
     * @param string $name
     * @return string
     */
    public function trimObjectName(string $name) : string
    {
        $schema = $this->getSchema();

        if ( ! isset($schema->maxObjectNameLength) )
        {
            return $name;
        }

        $name = trim($name, '_');
        $nameLength = StringHelper::strlen($name);

        if ( $nameLength > $schema->maxObjectNameLength )
        {
            $parts = array_filter(explode('_', $name));
            $totalParts = count($parts);
            $totalLetters = $nameLength - ($totalParts - 1);
            $maxLetters = $schema->maxObjectNameLength - ($totalParts - 1);

            // Consecutive underscores could have put this name over the top
            if ( $totalLetters > $maxLetters )
            {
                foreach ( $parts as $i => $part )
                {
                    $newLength = round($maxLetters * StringHelper::strlen($part) / $totalLetters);
                    $parts[$i] = mb_substr($part, 0, $newLength);
                }
            }

            $name = implode('_', $parts);

            // Just to be safe
            if ( StringHelper::strlen($name) > $schema->maxObjectNameLength )
            {
                $name = mb_substr($name, 0, $schema->maxObjectNameLength);
            }
        }

        return $name;
    }


    /**
     * Returns a primary key name based on the table and column names.
     *
     * @param string $table
     * @param string|array $columns
     * @return string
     */
    public function getPrimaryKeyName($table, $columns) : string
    {
        $table = $this->getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = StringHelper::split($columns);
        }
        $name = $this->tablePrefix . $table . '_' . implode('_', $columns) . '_pk';

        return $this->trimObjectName($name);
    }


    /**
     * Returns a foreign key name based on the table and column names.
     *
     * @param string $table
     * @param string|array $columns
     * @return string
     */
    public function getForeignKeyName($table, $columns) : string
    {
        $table = $this->getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = StringHelper::split($columns);
        }
        $name = $this->tablePrefix . $table . '_' . implode('_', $columns) . '_fk';

        return $this->trimObjectName($name);
    }


    /**
     * Returns an index name based on the table, column names, and whether
     * it should be unique.
     *
     * @param string $table
     * @param string|array $columns
     * @param bool $unique
     * @param bool $foreignKey
     * @return string
     */
    public function getIndexName($table, $columns, $unique = false, $foreignKey = false) : string
    {
        $table = $this->getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = StringHelper::split($columns);
        }
        $name = $this->tablePrefix . $table . '_' . implode('_', $columns) . ($unique ? '_unq' : '') . ($foreignKey ? '_fk' : '_idx');

        return $this->trimObjectName($name);
    }


    /**
     * Disable integrity check (Foreign Keys)
     */
    public function disableCheckIntegrity() : void
    {
        $this->createCommand()->checkIntegrity(false)->execute();
    }


    /**
     * Enable integrity check (Foreign Keys)
     */
    public function enableCheckIntegrity() : void
    {
        $this->createCommand()->checkIntegrity(true)->execute();
    }


    /*
    |--------------------------------------------------------------------------
    | BACKUP METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the full path for a new backup file
     */
    public function getBackupFilePath() : string
    {
        $filename = implode("_", [
            Transliteration::file(Yii::$app->name),
            date('YmdHi'),
            strtolower(StringHelper::randomString(10))
        ]);

        return Yii::getAlias("@backups/db") . DIRECTORY_SEPARATOR . "{$filename}.sql";
    }


    /**
     * Performs a backup operation. It will execute the default database schema specific backup
     * defined in `getDefaultBackupCommand()`, which uses `mysqldump` for MySQL.
     *
     * @return string|null The file path to the database backup or null if the backup failed
     * @throws Exception if the backupCommand config setting is false
     * @throws ShellCommandException in case of failure
     */
    public function backup(bool $is_zip = false) : ?string
    {
        $backup_file_path = $this->getBackupFilePath();

        return $this->backupTo($backup_file_path, $is_zip);
    }


    /**
     * Performs a backup operation. It will execute the default database schema specific backup
     * defined in `getDefaultBackupCommand()`, which uses `mysqldump` for MySQL.
     *
     * @param string|null The file path the database backup should be saved at or null if the backup failed
     * @throws Exception if the backupCommand config setting is false
     * @throws ShellCommandException in case of failure
     */
    public function backupTo($file_path, $is_zip = false) : ?string
    {
        $schema = $this->getSchema();
        $backupCommand = $schema->getDefaultBackupCommand();

        if ( $backupCommand === false )
        {
            throw new Exception('Database not backed up because the backup command is false.');
        }

        // Create the shell command
        $backupCommand = $this->parseCommandTokens($backupCommand, $file_path);
        $shellCommand = new ShellCommand();
        $shellCommand->setCommand($backupCommand);

        // If we don't have proc_open, maybe we've got exec
        if ( ! function_exists('proc_open') && function_exists('exec') )
        {
            $shellCommand->useExec = true;
        }

        // Execute command
        $is_success = $shellCommand->execute();

        // Delete any temp connection files that might have been created.
        $is_deleted = $schema->deleteDumpConfigFile();

        if ( ! $is_success )
        {
            $execCommand = $shellCommand->getExecCommand();
            throw new ShellCommandException($execCommand, $shellCommand->getExitCode(), $shellCommand->getStdErr());
        }

        // No ZIP file is needed, return the file path
        if ( ! $is_zip )
        {
            return $file_path;
        }

        // Generate backup in a ZIP?
        $backup_file = File::load($file_path);
        if ( ! $backup_file || ! $backup_file->exists() )
        {
            return null;
        }

        // ZIP the backup file and remove original
        $zip_file = $backup_file->zip(true);
        if ( ! $zip_file )
        {
            return null;
        }

        return $file_path .".zip";
    }


    /**
     * Returns the raw database table names that should be ignored by default.
     */
    public function getIgnoredBackupTables() : array
    {
        $vec_tables = [
            'user_session'
        ];

        if ( ! empty($this->backupIgnoredTables) )
        {
            $vec_tables = ArrayHelper::merge($vec_tables, $this->backupIgnoredTables);
        }

        return $vec_tables;
    }


   /*
    |--------------------------------------------------------------------------
    | PRIVATE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns a table name without the table prefix
     *
     * @param string $table
     * @return string
     */
    private function getTableNameWithoutPrefix($table)
    {
        if ( $this->tablePrefix )
        {
            if ( strpos($table, $this->tablePrefix) === 0 )
            {
                $table = substr($table, strlen($this->tablePrefix));
            }
        }

        return $table;
    }


    /**
     * Parses a database backup/restore command for config tokens
     *
     * @param string $command The command to parse tokens in
     * @param string $file The path to the backup file
     */
    private function parseCommandTokens($command, $file) : string
    {
        $vec_config = Yii::$app->config->getDb();
        $tokens = [
            '{file}'        => $file,
            '{port}'        => $vec_config['port'] ?? 3306,
            '{server}'      => $vec_config['server'] ?? '',
            '{user}'        => $vec_config['username'],
            '{password}'    => str_replace('$', '\\$', addslashes($vec_config['password'])),
            '{database}'    => $vec_config['database'] ?? '',
        ];

        return str_replace(array_keys($tokens), $tokens, $command);
    }
}
