<?php
/**
 * DB class file for Dz Framework
 */

namespace dezero\helpers;

use dezero\db\Connection;
use Dz;
use Yii;

/**
 * Helper class to work with database
 */
class Db
{
    /**
     * @var Connection|null;
     */
    private static ?Connection $db = null;


    /**
     * Returns the main application's DB connection.
     *
     * @return Connection
     */
    private static function db(): Connection
    {
        return self::$db ?? (self::$db = Yii::$app->getDb());
    }


    /**
     * Creates and executes an `INSERT` SQL statement.
     *
     * The method will properly escape the column names, and bind the values to be inserted.
     *
     * If the table contains `dateCreated`, `dateUpdated`, and/or `uid` columns, those values will be included
     * automatically, if not already set.
     *
     * @param string $table The table that new rows will be inserted into
     * @param array $columns The column data (name=>value) to be inserted into the table
     * @param Connection|null $db The database connection to use
     * @return int The number of rows affected by the execution
     * @throws DbException if execution failed
     */
    public static function insert(string $table, array $columns, Connection|null $db = null) : int
    {
        if ( $db === null )
        {
            $db = self::db();
        }

        return $db->createCommand()
            ->insert($table, $columns)
            ->execute();
    }



    /**
     * Creates and executes an `UPDATE` SQL statement.
     *
     * The method will properly escape the column names and bind the values to be updated.
     *
     * @param string $table The table to be updated
     * @param array $columns The column data (name => value) to be updated
     * @param string|array $condition The condition that will be put in the `WHERE` part. Please
     * refer to [[Query::where()]] on how to specify condition
     * @param array $params The parameters to be bound to the command
     * @param bool $updateTimestamp Whether the `dateUpdated` column should be updated, if the table has one.
     * @param Connection|null $db The database connection to use
     * @return int The number of rows affected by the execution
     * @throws DbException if execution failed
     */
    public static function update(string $table, array $columns, $condition = '', array $params = [], bool $updateTimestamp = true, ?Connection $db = null) : int {
        if ( $db === null )
        {
            $db = self::db();
        }

        return $db->createCommand()
            ->update($table, $columns, $condition, $params, $updateTimestamp)
            ->execute();
    }


    /**
     * Creates and executes a `DELETE` SQL statement.
     *
     * @param string $table the table where the data will be deleted from
     * @param string|array $condition the conditions that will be put in the `WHERE` part. Please
     * refer to [[Query::where()]] on how to specify conditions.
     * @param array $params the parameters to be bound to the query.
     * @param Connection|null $db The database connection to use
     * @return int The number of rows affected by the execution
     * @throws DbException if execution failed
     */
    public static function delete(string $table, $condition = '', array $params = [], ?Connection $db = null): int {
        if ( $db === null )
        {
            $db = self::db();
        }

        return $db->createCommand()
            ->delete($table, $condition, $params)
            ->execute();
    }
}
