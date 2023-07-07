<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use dezero\behaviors\UuidBehavior;
use dezero\contracts\TitleInterface;
use dezero\helpers\ArrayHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of Entity objects.
 */
abstract class ActiveRecord extends \dezero\db\ActiveRecord implements TitleInterface
{
    use DisableTrait;
    use EnableTrait;
    use StatusTrait;


    /**
     * Return entity type
     */
    public function getEntityType() : string
    {
        return $this->tableName();
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                [
                    'class' => BlameableBehavior::class,
                    'createdByAttribute' => 'created_user_id',
                    'updatedByAttribute' => 'updated_user_id',
                ],
                [
                    'class' => TimestampBehavior::class,
                    'createdAtAttribute' => 'created_date',
                    'updatedAtAttribute' => 'updated_date',
                ],
                UuidBehavior::class
            ]
        );
    }
}
