<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use dezero\contracts\TitleInterface;
use Yii;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of Entity objects.
 */
abstract class ActiveRecord extends \dezero\db\ActiveRecord implements TitleInterface
{
    use DisableTrait;


    /**
     * Return entity type
     */
    public function getEntityType() : string
    {
        return $this->tableName();
    }
}
