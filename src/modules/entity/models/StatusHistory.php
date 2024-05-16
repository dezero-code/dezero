<?php
/**
 * StatusHistory model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models;

use dezero\behaviors\TimestampBehavior;
use dezero\behaviors\UuidBehavior;
use dezero\helpers\ArrayHelper;
use dezero\modules\entity\models\query\StatusHistoryQuery;
use dezero\modules\entity\models\base\StatusHistory as BaseStatusHistory;
use dezero\modules\entity\models\Entity;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * StatusHistory model class for table "entity_status_history".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $status_history_id
 * @property string $entity_type
 * @property string $entity_uuid
 * @property int $entity_source_id
 * @property string $status_type
 * @property string $comments
 * @property int $created_date
 * @property int $created_user_id
 */
class StatusHistory extends BaseStatusHistory
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['entity_type', 'status_type'], 'required'],
            'integerFields' => [['entity_source_id', 'created_date', 'created_user_id'], 'integer'],
            'stringFields' => [['comments'], 'string'],

            // Max length rules
            'max32' => [['status_type'], 'string', 'max' => 32],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['entity_type'], 'string', 'max' => 128],

            // Default NULL
            'defaultNull' => [['entity_source_id', 'comments'], 'default', 'value' => null],
        ];
        */

        return ArrayHelper::merge(
            parent::rules(),
            [
                // Custom validation rules
            ]
        );
    }


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                TimestampBehavior::class,
                UuidBehavior::class
            ]
        );
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'status_history_id' => Yii::t('backend', 'Status History ID'),
            'entity_type' => Yii::t('backend', 'Entity Type'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
            'entity_source_id' => Yii::t('backend', 'Entity Source ID'),
            'status_type' => Yii::t('backend', 'Status Type'),
            'comments' => Yii::t('backend', 'Comments'),
            'created_date' => Yii::t('backend', 'Created Date'),
            'created_user_id' => Yii::t('backend', 'Created User ID'),
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getCreatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'created_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getEntity() : ActiveQueryInterface
    {
        return $this->hasOne(Entity::class, ['entity_uuid' => 'entity_uuid']);
    }


    /*
    |--------------------------------------------------------------------------
    | TITLE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->entity_type;
    }
}
