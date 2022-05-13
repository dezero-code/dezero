<?php
/**
 * Database QueryBuilder class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db\mysql;

use Yii;

/**
 * QueryBuilder is the query builder for MySQL databases.
 */
class QueryBuilder extends \yii\db\mysql\QueryBuilder
{
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


    /**
     * @inheritdoc
     *
     * @param string $table the name of the table to be created. The name will be properly quoted by the method.
     * @param array $columns the columns (name => definition) in the new table.
     * @param string|null $options additional SQL fragment that will be appended to the generated SQL.
     * @return string the SQL statement for creating a new DB table.
     */
    public function createTable($table, $columns, $options = null) : string
    {
        // Default to InnoDb
        if ( $options === null || ! preg_match('/\bENGINE\b/i', $options) )
        {
            $options = ($options !== null ? $options . ' ' : '') . 'ENGINE = InnoDb';
        }

        // Use the default charset and collation
        if ( ! preg_match('/\bCHARACTER +SET\b/i', $options) )
        {
            $options .= " DEFAULT CHARACTER SET = {$this->db->charset}";
        }

        if ( $this->db->collation !== null && ! preg_match('/\bCOLLATE\b/i', $options) )
        {
            $options .= " DEFAULT COLLATE = $this->db->collation";
        }

        return parent::createTable($table, $columns, $options);
    }
}
