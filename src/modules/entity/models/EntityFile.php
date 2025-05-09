<?php
/**
 * EntityFile model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models;

use dezero\behaviors\TimestampBehavior;
use dezero\behaviors\WeightBehavior;
use dezero\helpers\ArrayHelper;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\asset\models\AssetImage;
use dezero\modules\entity\models\query\EntityFileQuery;
use dezero\modules\entity\models\base\EntityFile as BaseEntityFile;
use dezero\modules\entity\models\Entity;
use yii\db\ActiveQueryInterface;
use user\models\User;
use Yii;

/**
 * EntityFile model class for table "entity_file".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $entity_file_id
 * @property int $file_id
 * @property string $entity_uuid
 * @property string $entity_type
 * @property int $entity_source_id
 * @property string $relation_type
 * @property int $weight
 * @property int $created_date
 * @property int $created_user_id
 * @property int $updated_date
 * @property int $updated_user_id
 */
class EntityFile extends BaseEntityFile
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['file_id', 'entity_type', 'relation_type'], 'required'],
            'integerFields' => [['file_id', 'entity_source_id', 'weight', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            
            // Max length rules
            'max32' => [['relation_type'], 'string', 'max' => 32],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['entity_type'], 'string', 'max' => 128],
            
            // Default NULL
            'defaultNull' => [['entity_source_id'], 'default', 'value' => null],
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

                // Weight
                [
                    'class' => WeightBehavior::class,
                    'vec_attributes' => ['entity_source_id', 'entity_type', 'relation_type']
                ]
            ]
        );
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'entity_file_id' => Yii::t('backend', 'Entity File ID'),
            'file_id' => Yii::t('backend', 'File ID'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
            'entity_type' => Yii::t('backend', 'Entity Type'),
            'entity_source_id' => Yii::t('backend', 'Entity Source ID'),
            'relation_type' => Yii::t('backend', 'Relation Type'),
            'weight' => Yii::t('backend', 'Weight'),
            'created_date' => Yii::t('backend', 'Created Date'),
            'created_user_id' => Yii::t('backend', 'Created User ID'),
            'updated_date' => Yii::t('backend', 'Updated Date'),
            'updated_user_id' => Yii::t('backend', 'Updated User ID'),
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
    public function getAssetFile() : ActiveQueryInterface
    {
        return $this->hasOne(AssetFile::class, ['file_id' => 'file_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getAssetImage() : ActiveQueryInterface
    {
        return $this->hasOne(AssetImage::class, ['file_id' => 'file_id']);
    }


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



    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_user_id']);
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
        return $this->entity_uuid;
    }
}
