<?php
/**
 * Database Connection class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db;

use dezero\db\mysql\QueryBuilder as MysqlQueryBuilder;
use dezero\db\mysql\Schema as MysqlSchema;
use dezero\helpers\Str;
use Yii;
use yii\helpers\ArrayHelper;

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
        $nameLength = Str::strlen($name);

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
                    $newLength = round($maxLetters * Str::strlen($part) / $totalLetters);
                    $parts[$i] = mb_substr($part, 0, $newLength);
                }
            }

            $name = implode('_', $parts);

            // Just to be safe
            if ( Str::strlen($name) > $schema->maxObjectNameLength )
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
    public function getPrimaryKeyName($table, $columns)
    {
        $table = $this->_getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = Str::split($columns);
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
    public function getForeignKeyName($table, $columns)
    {
        $table = $this->_getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = Str::split($columns);
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
    public function getIndexName($table, $columns, $unique = false, $foreignKey = false)
    {
        $table = $this->_getTableNameWithoutPrefix($table);
        if ( is_string($columns) )
        {
            $columns = Str::split($columns);
        }
        $name = $this->tablePrefix . $table . '_' . implode('_', $columns) . ($unique ? '_unq' : '') . ($foreignKey ? '_fk' : '_idx');

        return $this->trimObjectName($name);
    }


    /**
     * Returns the raw database table names that should be ignored by default.
     *
     * @return array
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


    // Private Methods
    // =========================================================================

    /**
     * Returns a table name without the table prefix
     *
     * @param string $table
     * @return string
     */
    private function _getTableNameWithoutPrefix($table)
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
}
