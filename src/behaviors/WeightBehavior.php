<?php
/**
 * WeightBehavior class file
 *
 * Automatically generate and save the weight value
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://dezero.es/
 * @copyright Copyright &copy; 2023 Dezero
 */
namespace dezero\behaviors;

use dezero\db\Query;
use dezero\helpers\StringHelper;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class WeightBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive the weight value
     */
    public $weight_attribute = 'weight';


    /**
     * @var array the condition attributes to get the weight value
     */
    public $vec_attributes = [];


    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
        ];
    }


    /**
     * Before insert event
     */
    public function beforeInsert($event)
    {
        $model = $event->sender;

        // Save DATE attributes
        if ( $model->hasAttribute($this->weight_attribute) && ( is_null($model->{$this->weight_attribute}) || $model->{$this->weight_attribute} <= 1 ) )
        {
            $model->setAttribute($this->weight_attribute, $this->getNextWeight());
        }
    }


    /**
     * Get last weight value
     */
    public function getNextWeight(array $vec_attributes = []) : int
    {
        $model = $this->owner;
        $model_class = $model::className();

        // Generate WHERE conditions
        $vec_conditions = [];
        if ( empty($vec_attributes) && !empty($this->vec_attributes))
        {
            $vec_attributes = $this->vec_attributes;
        }
        if ( !empty($vec_attributes) )
        {
            foreach ( $vec_attributes as $attribute_name )
            {
                if ( $model->hasAttribute($attribute_name) )
                {
                    $vec_conditions[$attribute_name] = $model->getAttribute($attribute_name);
                }
            }
        }

        // Get last weight value for given conditions
        $weight = (new Query())
            ->select($this->weight_attribute)
            ->from($model_class::tableName())
            ->where($vec_conditions)
            ->orderBy([
                $this->weight_attribute => SORT_DESC
            ])
            ->scalar();

        if ( $weight === false || $weight === null )
        {
            return 1;
        }

        return (int)$weight + 1;
    }
}
