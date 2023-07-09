<?php
/**
 * TimestampBehavior class file
 *
 * Include:
 *  - "TimestampBehavior" automatically fills the specified attributes with the current timestamp
 *  - "BlameableBehavior" that automatically fills the specified attributes with the current user ID
 *
 * This version check if date or user attribute exists in the model
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://dezero.es/
 * @copyright Copyright &copy; 2023 Dezero
 */
namespace dezero\behaviors;

use dezero\helpers\StringHelper;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;

class TimestampBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive timestamp value
     * Set this property to false if you do not want to record the creation time.
     */
    public $created_date_attribute = 'created_date';


    /**
     * @var string the attribute that will receive timestamp value.
     * Set this property to false if you do not want to record the update time.
     */
    public $updated_date_attribute = 'updated_date';


    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the creator ID.
     */
    public $created_user_attribute = 'created_user_id';


    /**
     * @var string the attribute that will receive current user ID value
     * Set this property to false if you do not want to record the updater ID.
     */
    public $updated_user_attribute = 'updated_user_id';


    /**
     * {@inheritdoc}
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeInsert',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeUpdate',
        ];
    }


    /**
     * Before insert event
     */
    public function beforeInsert($event)
    {
        $model = $event->sender;

        // Save DATE attributes
        $now = $this->now();
        if ( $model->hasAttribute($this->created_date_attribute) && is_null($model->{$this->created_date_attribute}) )
        {
            $model->setAttribute($this->created_date_attribute, $now);
        }
        if ( $model->hasAttribute($this->updated_date_attribute) )
        {
            $model->setAttribute($this->updated_date_attribute, $now);
        }


        // Save USER attributes
        $current_user = $this->currentUser();
        if ( $model->hasAttribute($this->created_user_attribute) && is_null($model->{$this->created_user_attribute}) )
        {
            $model->setAttribute($this->created_user_attribute, $current_user);
        }
        if ( $model->hasAttribute($this->updated_user_attribute) )
        {
            $model->setAttribute($this->updated_user_attribute, $current_user);
        }
    }


    /**
     * Before update event
     */
    public function beforeUpdate($event)
    {
        $model = $event->sender;

        // Save DATE attributes
        if ( $model->hasAttribute($this->updated_date_attribute) )
        {
            $model->setAttribute($this->updated_date_attribute, $this->now());
        }

        // Save USER attribute
        if ( $model->hasAttribute($this->updated_user_attribute) )
        {
            $model->setAttribute($this->updated_user_attribute, $this->currentUser());
        }
    }


    /**
     * Return current timestamp
     */
    private function now()
    {
        return time();
    }


    /**
     * Return current user id
     */
    private function currentUser()
    {
        $user_id = null;
        if ( Yii::$app->has('user') )
        {
            $user_id = Yii::$app->get('user')->id;
        }

        // Default value "1"
        return ( $user_id !== null ) ? $user_id : 1;
    }
}
