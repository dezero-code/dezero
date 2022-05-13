<?php
/**
 * Database ColumnSchema class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db\mysql;

use Yii;

/**
 * ColumnSchema class describes the metadata of a column in a database table.
 *
 * Class ColumnSchema for MySQL databases.
 */
class ColumnSchema extends \yii\db\mysql\ColumnSchema
{
    /**
     * @inheritdoc
     */
    protected function typecast($value)
    {
        // Prevent Yii from typecasting our custom text column types to null
        if (
            $value === '' &&
            in_array($this->type, [
                Schema::TYPE_TINYTEXT,
                Schema::TYPE_MEDIUMTEXT,
                Schema::TYPE_LONGTEXT,
            ], true)
        ) {
            return '';
        }

        return parent::typecast($value);
    }
}
