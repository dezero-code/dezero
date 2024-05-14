<?php
/**
 * Migration class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db;

use dezero\db\mysql\Schema as MysqlSchema;
use Yii;
use yii\db\ColumnSchemaBuilder;

/**
 * Migration is the base class for representing a database migration.
 */
class Migration extends \yii\db\Migration
{
    /**
     * Creates and executes a SQL statement for dropping a DB table, if it exists.
     *
     * @param string $table The table to be dropped. The name will be properly quoted by the method.
     */
    public function dropTableIfExists(string $table, bool $isDisableCheckIntegrity = false) : void
    {
        // Disable integrity check (Foreign Keys) temporally
        if ( $isDisableCheckIntegrity )
        {
            $this->db->disableCheckIntegrity();
        }

        $time = $this->beginCommand("dropping $table if it exists");
        $this->db->createCommand()
            ->dropTableIfExists($table)
            ->execute();
        $this->endCommand($time);

        // Enable integrity check (Foreign Keys) again
        if ( $isDisableCheckIntegrity )
        {
            $this->db->enableCheckIntegrity();
        }
    }


    /**
     * @inheritdoc
     *
     * @param string|null $name the name of the primary key constraint. If null, a name will be automatically generated.
     * @param string $table the table that the primary key constraint will be added to.
     * @param string|array $columns comma separated string or array of columns that the primary key will consist of.
     */
    public function addPrimaryKey($name, $table, $columns) : void
    {
        parent::addPrimaryKey($name ?? $this->db->getPrimaryKeyName($table, $columns), $table, $columns);
    }


    /**
     * @inheritdoc
     *
     * @param string|null $name the name of the foreign key constraint. If null, a name will be automatically generated.
     * @param string $table the table that the foreign key constraint will be added to.
     * @param string|array $columns the name of the column to that the constraint will be added on. If there are multiple columns, separate them with commas or use an array.
     * @param string $refTable the table that the foreign key references to.
     * @param string|array $refColumns the name of the column that the foreign key references to. If there are multiple columns, separate them with commas or use an array.
     * @param string $delete the ON DELETE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     * @param string $update the ON UPDATE option. Most DBMS support these options: RESTRICT, CASCADE, NO ACTION, SET DEFAULT, SET NULL
     */
    public function addForeignKey($name, $table, $columns, $refTable, $refColumns, $delete = null, $update = null) : void
    {
        parent::addForeignKey($name ?? $this->db->getForeignKeyName($table, $columns), $table, $columns, $refTable, $refColumns, $delete, $update);
    }


    /**
     * @inheritdoc
     *
     * @param string|null $name the name of the index. The name will be properly quoted by the method. If null, a name will be automatically generated.
     * @param string $table the table that the new index will be created for. The table name will be properly quoted by the method.
     * @param string|array $columns the column(s) that should be included in the index. If there are multiple columns, please separate them
     * by commas or use an array. Each column name will be properly quoted by the method. Quoting will be skipped for column names that
     * include a left parenthesis "(".
     * @param bool $unique whether to add UNIQUE constraint on the created index.
     */
    public function createIndex($name, $table, $columns, $unique = false) : void
    {
        parent::createIndex($name ?? $this->db->getIndexName($table, $columns, $unique), $table, $columns, $unique);
    }


    /**
     * Creates and executes an INSERT SQL statement with multiple data.
     * The method will properly escape the column names, and bind the values to be inserted.
     * @param string $table the table that new rows will be inserted into.
     * @param array $data an array of various column data (name=>value) to be inserted into the table.
     *
     * @source Port from Yii1 CDbMigration::insertMultple
     */
    public function insertMultiple(string $table, array $data) : void
    {
        if ( !empty($data) )
        {
            foreach ( $data as $columns )
            {
                $this->insert($table, $columns);
            }
        }
    }


    // -------------------------------------------------------------------------
    // Schema Builder Methods
    // -------------------------------------------------------------------------

    /**
     * @inheritdoc
     *
     * @param int|null $length column size or precision definition.
     * This parameter will be ignored if not supported by the DBMS.
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     * @since 2.0.6
     */
    public function primaryKey($length = null) : ColumnSchemaBuilder
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_PK, $length)->unsigned();
    }


    /**
     * Creates a tinytext column for MySQL, or text column for others.
     *
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function tinyText() : ColumnSchemaBuilder
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_TINYTEXT);
    }


    /**
     * Creates a mediumtext column for MySQL, or text column for others.
     *
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function mediumText() : ColumnSchemaBuilder
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_MEDIUMTEXT);
    }


    /**
     * Creates a longtext column for MySQL, or text column for others.
     *
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function longText() : ColumnSchemaBuilder
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_LONGTEXT);
    }


    /**
     * Creates an enum column for MySQL and PostgreSQL, or a string column with a check constraint for others.
     *
     * @param string $columnName The column name
     * @param string[] $values The allowed column values
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function enum(string $columnName, array $values) : ColumnSchemaBuilder
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_ENUM, $values);
    }


    /**
     * @inheritdoc
     *
     * Changed from DATE type to INTEGER UNSIGNED
     */
    public function date()
    {
        // return $this->db->getSchema()->createColumnSchemaBuilder(Schema::TYPE_DATE);
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_INTEGER)->unsigned();
    }


    /**
     * Creates a DATE MYSQL column.
     */
    public function dateRaw()
    {
        return $this->db->getSchema()->createColumnSchemaBuilder(MysqlSchema::TYPE_DATE);
    }


    /**
     * Shortcut for creating an UUID column
     *
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function uuid() : ColumnSchemaBuilder
    {
        return $this->char(36)->notNull()->defaultValue('0');
    }


    /**
     * Shortcut for creating a Language column
     *
     * @return ColumnSchemaBuilder the column instance which can be further customized.
     */
    public function language()
    {
        return $this->string(6);
    }
}
