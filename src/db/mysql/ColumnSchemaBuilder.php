<?php
/**
 * Database ColumnSchemaBuilder class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db\mysql;

use Yii;

/**
 * ColumnSchemaBuilder helps to define database schema types using a PHP interface.
 *
 * It is the schema builder for MySQL databases.
 */
class ColumnSchemaBuilder extends \yii\db\mysql\ColumnSchemaBuilder
{
    /**
     * @inheritdoc
     */
    protected function buildLengthString() : string
    {
        if ( $this->type == Schema::TYPE_ENUM )
        {
            $schema = Yii::$app->getDb()->getSchema();
            $str = '(';
            foreach ( $this->length as $i => $value )
            {
                if ( $i != 0 )
                {
                    $str .= ',';
                }
                $str .= $schema->quoteValue($value);
            }
            $str .= ')';

            return $str;
        }

        return parent::buildLengthString();
    }
}
