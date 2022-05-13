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
}
