<?php
/**
 * Batch model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\sync\models;

use dezero\behaviors\TimestampBehavior;
use dezero\entity\ActiveRecord;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\Log;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\entity\models\Entity;
use dezero\modules\sync\models\base\Batch as BaseBatch;
use dezero\modules\sync\models\query\BatchQuery;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Batch model class for table "batch".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $batch_id
 * @property string $batch_type
 * @property string $name
 * @property string $description
 * @property int $total_items
 * @property int $total_errors
 * @property int $total_warnings
 * @property string $summary_json
 * @property string $results_json
 * @property int $total_operations
 * @property int $last_operation
 * @property int $item_starting_num
 * @property int $item_ending_num
 * @property int $file_id
 * @property string $entity_uuid
 * @property string $entity_type
 * @property int $entity_source_id
 * @property int $created_date
 * @property int $created_user_id
 * @property int $updated_date
 * @property int $updated_user_id
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
 * @property User $createdUser
 * @property Entity $entityUu
 * @property AssetFile $file
 * @property User $updatedUser
 */
class Batch extends BaseBatch
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['batch_type', 'name'], 'required'],
            'integerFields' => [['total_items', 'total_errors', 'total_warnings', 'total_operations', 'last_operation', 'item_starting_num', 'item_ending_num', 'file_id', 'entity_source_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'stringFields' => [['summary_json', 'results_json'], 'string'],
            
            // Max length rules
            'max32' => [['batch_type'], 'string', 'max' => 32],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['name', 'entity_type'], 'string', 'max' => 128],
            'max255' => [['description'], 'string', 'max' => 255],
            
            // Default NULL
            'defaultNull' => [['description', 'summary_json', 'results_json', 'file_id', 'entity_uuid', 'entity_type', 'entity_source_id'], 'default', 'value' => null],
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
                TimestampBehavior::class
            ]
        );
    }


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'batch_id' => Yii::t('backend', 'Batch ID'),
            'batch_type' => Yii::t('backend', 'Batch Type'),
            'name' => Yii::t('backend', 'Name'),
            'description' => Yii::t('backend', 'Description'),
            'total_items' => Yii::t('backend', 'Total Items'),
            'total_errors' => Yii::t('backend', 'Total Errors'),
            'total_warnings' => Yii::t('backend', 'Total Warnings'),
            'summary_json' => Yii::t('backend', 'Summary Json'),
            'results_json' => Yii::t('backend', 'Results Json'),
            'total_operations' => Yii::t('backend', 'Total Operations'),
            'last_operation' => Yii::t('backend', 'Last Operation'),
            'item_starting_num' => Yii::t('backend', 'Item Starting Num'),
            'item_ending_num' => Yii::t('backend', 'Item Ending Num'),
            'file_id' => Yii::t('backend', 'File ID'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
            'entity_type' => Yii::t('backend', 'Entity Type'),
            'entity_source_id' => Yii::t('backend', 'Entity Source ID'),
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
    public function getFile() : ActiveQueryInterface
    {
        return $this->hasOne(AssetFile::class, ['file_id' => 'file_id']);
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
    | GETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return the summary content into an array
     */
    public function getSummary() : array
    {
        return !empty($this->summary_json) ? Json::decode($this->summary_json) : [];
    }


    /**
     * Return the results content into an array
     */
    public function getResults() : array
    {
        return !empty($this->results_json) ? Json::decode($this->results_json) : [];
    }


    /**
     * Return formatted endpoint
     */
    public function getEndpoint(?string $endpoint = null) : string
    {
        if ( $endpoint === null )
        {
            $vec_summary = $this->getSummary();
            if ( !empty($vec_summary) && isset($vec_summary['request_endpoint']) )
            {
                $endpoint = $vec_summary['request_endpoint'];
            }
        }

        if ( $endpoint === null )
        {
            return '';
        }

        if ( preg_match("/\_\_\_/", $endpoint) )
        {
            $vec_endpoint = explode("___", $endpoint);
            return $vec_endpoint[0] .' '. $vec_endpoint[1];
        }

        return $endpoint;
    }



    /*
    |--------------------------------------------------------------------------
    | ENTITY METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Link an Entity model with last ApiLog model
     */
    public function linkEntity(ActiveRecord $entity_model, bool $is_save = true) : bool
    {
        // Entity type
        $this->setAttributes([
            'entity_uuid'       => ! $entity_model->getIsNewRecord() && $entity_model->hasAttribute('entity_uuid') ? $entity_model->getAttribute('entity_uuid') : null,
            'entity_type'       => $entity_model->getEntityType(),
            'entity_source_id'  => $entity_model->getSourceId()
        ]);

        if ( $is_save && ! $this->save() )
        {
            // Some error saving log into database
            Log::saveModelError($this);

            return false;
        }

        return true;
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
        return "{$this->batch_type} - {$this->name}";
    }
}
