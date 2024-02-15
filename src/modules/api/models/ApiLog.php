<?php
/**
 * ApiLog model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\models;

use dezero\behaviors\TimestampBehavior;
use dezero\contracts\EntityInterface;
use dezero\entity\ActiveRecord;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\Log;
use dezero\modules\api\models\base\ApiLog as BaseApiLog;
use dezero\modules\api\models\query\ApiLogQuery;
use dezero\modules\entity\models\Entity;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * ApiLog model class for table "api_log".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $api_log_id
 * @property string $api_type
 * @property string $api_name
 * @property string $request_type
 * @property string $request_url
 * @property string $request_endpoint
 * @property string $request_input_json
 * @property string $request_hostname
 * @property int $response_http_code
 * @property string $response_json
 * @property string $entity_uuid
 * @property string $entity_type
 * @property int $entity_source_id
 * @property int $created_date
 * @property int $created_user_id
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
 * @property User $createdUser
 * @property Entity $entityUuid
 */
class ApiLog extends BaseApiLog implements EntityInterface
{
    public const API_TYPE_CLIENT = 'client';
    public const API_TYPE_SERVER = 'server';
    public const REQUEST_TYPE_GET = 'GET';
    public const REQUEST_TYPE_POST = 'POST';
    public const REQUEST_TYPE_PUT = 'PUT';
    public const REQUEST_TYPE_DELETE = 'DELETE';


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['request_url', 'request_endpoint'], 'required'],
            'integerFields' => [['response_http_code', 'entity_source_id', 'created_date', 'created_user_id'], 'integer'],
            'stringFields' => [['request_input_json', 'response_json'], 'string'],
            
            // Max length rules
            'max32' => [['api_name'], 'string', 'max' => 32],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['request_endpoint', 'request_hostname', 'entity_type'], 'string', 'max' => 128],
            'max512' => [['request_url'], 'string', 'max' => 512],
            
            // ENUM rules
            'apiTypeList' => ['api_type', 'in', 'range' => [
                   self::API_TYPE_CLIENT,
                   self::API_TYPE_SERVER,
               ]
            ],
            'requestTypeList' => ['request_type', 'in', 'range' => [
                    self::REQUEST_TYPE_GET,
                    self::REQUEST_TYPE_POST,
                    self::REQUEST_TYPE_PUT,
                    self::REQUEST_TYPE_DELETE,
                ]
            ],
            
            // Default NULL
            'defaultNull' => [['request_input_json', 'request_hostname', 'response_json', 'entity_uuid', 'entity_type', 'entity_source_id'], 'default', 'value' => null],
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
            'api_log_id' => Yii::t('backend', 'Api Log ID'),
            'api_type' => Yii::t('backend', 'Api Type'),
            'api_name' => Yii::t('backend', 'Api Name'),
            'request_type' => Yii::t('backend', 'Request Type'),
            'request_url' => Yii::t('backend', 'Request Url'),
            'request_endpoint' => Yii::t('backend', 'Request Endpoint'),
            'request_input_json' => Yii::t('backend', 'Request Input Json'),
            'request_hostname' => Yii::t('backend', 'Request Hostname'),
            'response_http_code' => Yii::t('backend', 'Response Http Code'),
            'response_json' => Yii::t('backend', 'Response Json'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
            'entity_type' => Yii::t('backend', 'Entity Type'),
            'entity_source_id' => Yii::t('backend', 'Entity Source ID'),
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
    | ENUM LABELS
    |--------------------------------------------------------------------------
    */

    /**
    * Get "api_type" labels
    */
    public function api_type_labels() : array
    {
       return [
           self::API_TYPE_CLIENT => Yii::t('backend', 'Client'),
           self::API_TYPE_SERVER => Yii::t('backend', 'Server'),
       ];
    }


    /**
    * Get "api_type" specific label
    */
    public function api_type_label(?string $api_type = null) : string
    {
       $api_type = ( $api_type === null ) ? $this->api_type : $api_type;
       $vec_labels = $this->api_type_labels();

       return isset($vec_labels[$api_type]) ? $vec_labels[$api_type] : '';
    }


    /**
     * Get "request_type" labels
     */
    public function request_type_labels() : array
    {
        return [
            self::REQUEST_TYPE_GET => Yii::t('backend', 'GET'),
            self::REQUEST_TYPE_POST => Yii::t('backend', 'POST'),
            self::REQUEST_TYPE_PUT => Yii::t('backend', 'PUT'),
            self::REQUEST_TYPE_DELETE => Yii::t('backend', 'DELETE'),
        ];
    }


    /**
     * Get "request_type" specific label
     */
    public function request_type_label(?string $request_type = null) : string
    {
        $request_type = ( $request_type === null ) ? $this->request_type : $request_type;
        $vec_labels = $this->request_type_labels();

        return isset($vec_labels[$request_type]) ? $vec_labels[$request_type] : '';
    }


    /*
    |--------------------------------------------------------------------------
    | GETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return formatted endpoint
     */
    public function getEndpoint(?string $endpoint = null) : string
    {
        if ( $endpoint === null )
        {
            $endpoint = $this->request_endpoint;
        }

        if ( preg_match("/\_\_\_/", $endpoint) )
        {
            $vec_endpoint = explode("___", $endpoint);
            return $vec_endpoint[0] .' '. $vec_endpoint[1];
        }

        return $endpoint;
    }


    /**
     * Return the response into an array
     */
    public function getInput() : array
    {
        if ( empty($this->request_input_json) )
        {
            return [];
        }

        $vec_input = Json::decode($this->request_input_json);
        if ( !is_array($vec_input) )
        {
            $vec_input = Json::decode($vec_input);
        }

        return $vec_input;
    }


    /**
     * Return the response into an array
     */
    public function getResponse() : array
    {
        return !empty($this->response_json) ? Json::decode($this->response_json) : [];
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
        return "{$this->api_name} ({$this->api_type}) - $this->request_endpoint";
    }
}
