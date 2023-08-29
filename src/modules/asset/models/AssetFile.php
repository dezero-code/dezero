<?php
/**
 * AssetFile model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\models;

use dezero\base\File;
use dezero\entity\ActiveRecord as EntityActiveRecord;
use dezero\helpers\ArrayHelper;
use dezero\modules\asset\models\query\AssetFileQuery;
use dezero\modules\asset\models\base\AssetFile as BaseAssetFile;
use dezero\modules\asset\services\UploadFileService;
use dezero\modules\entity\models\EntityFile;
use Dz;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * AssetFile model class for table "asset_file".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $file_id
 * @property string $file_name
 * @property string $file_path
 * @property string $file_mime
 * @property int $file_size
 * @property string $file_options
 * @property string $asset_type
 * @property string $title
 * @property string $description
 * @property string $original_file_name
 * @property string $reference_entity_uuid
 * @property string $reference_entity_type
 * @property int $created_date
 * @property int $created_user_id
 * @property int $updated_date
 * @property int $updated_user_id
 * @property string $entity_uuid
 */
class AssetFile extends BaseAssetFile
{
    public const ASSET_TYPE_IMAGE = 'image';
    public const ASSET_TYPE_DOCUMENT = 'document';
    public const ASSET_TYPE_VIDEO = 'video';
    public const ASSET_TYPE_OTHER = 'other';


    /**
     * @var \dezero\base\File
     */
    public $file;


    /**
     * @var \dezero\base\File
     */
    private $uploadedFile;


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['file_name', 'file_path', 'file_mime'], 'required'],
            'integerFields' => [['file_size', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'stringFields' => [['description'], 'string'],
            
            // Max length rules
            'max36' => [['reference_entity_uuid', 'entity_uuid'], 'string', 'max' => 36],
            'max128' => [['file_name', 'file_mime', 'original_file_name', 'reference_entity_type'], 'string', 'max' => 128],
            'max255' => [['file_path', 'file_options', 'title'], 'string', 'max' => 255],
            
            // ENUM rules
            'assetTypeList' => ['asset_type', 'in', 'range' => [
                    self::ASSET_TYPE_IMAGE,
                    self::ASSET_TYPE_DOCUMENT,
                    self::ASSET_TYPE_VIDEO,
                    self::ASSET_TYPE_OTHER,
                ]
            ],
            
            // Default NULL
            'defaultNull' => [['file_options', 'title', 'description', 'original_file_name', 'reference_entity_uuid', 'reference_entity_type'], 'default', 'value' => null],
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
     *
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }
    */


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'file_id' => Yii::t('assetfile', 'File ID'),
            'file_name' => Yii::t('assetfile', 'File Name'),
            'file_path' => Yii::t('assetfile', 'File Path'),
            'file_mime' => Yii::t('assetfile', 'File Mime'),
            'file_size' => Yii::t('assetfile', 'File Size'),
            'file_options' => Yii::t('assetfile', 'File Options'),
            'asset_type' => Yii::t('assetfile', 'Asset Type'),
            'title' => Yii::t('assetfile', 'Title'),
            'description' => Yii::t('assetfile', 'Description'),
            'original_file_name' => Yii::t('assetfile', 'Original File Name'),
            'reference_entity_uuid' => Yii::t('assetfile', 'Reference Entity Uuid'),
            'reference_entity_type' => Yii::t('assetfile', 'Reference Entity Type'),
            'created_date' => Yii::t('assetfile', 'Created Date'),
            'created_user_id' => Yii::t('assetfile', 'Created User ID'),
            'updated_date' => Yii::t('assetfile', 'Updated Date'),
            'updated_user_id' => Yii::t('assetfile', 'Updated User ID'),
            'entity_uuid' => Yii::t('assetfile', 'Entity Uuid'),
        ];
    }


   /*
    |--------------------------------------------------------------------------
    | ENUM LABELS
    |--------------------------------------------------------------------------
    */

    /**
     * Get "asset_type" labels
     */
    public function asset_type_labels() : array
    {
        return [
            self::ASSET_TYPE_IMAGE => Yii::t('assetfile', 'Image'),
            self::ASSET_TYPE_DOCUMENT => Yii::t('assetfile', 'Document'),
            self::ASSET_TYPE_VIDEO => Yii::t('assetfile', 'Video'),
            self::ASSET_TYPE_OTHER => Yii::t('assetfile', 'Other'),
        ];
    }


    /**
     * Get "asset_type" specific label
     */
    public function asset_type_label(?string $asset_type = null) : string
    {
        $asset_type = ( $asset_type === null ) ? $this->asset_type : $asset_type;
        $vec_labels = $this->asset_type_labels();

        return isset($vec_labels[$asset_type]) ? $vec_labels[$asset_type] : '';
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
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_user_id']);
    }


    /*
    |--------------------------------------------------------------------------
    | FILE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Load File class object
     */
    public function loadFile() : bool
    {
        if ( $this->file === null )
        {
            $this->file = File::load($this->getRelativePath());
        }

        return $this->file && $this->file->exists();
    }


    /**
     * Return relative path
     */
    public function getRelativePath() : string
    {
        return $this->file_path . $this->file_name;
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Upload a file
     */
    public function uploadFile(EntityActiveRecord $model, string $attribute, ?string $destination_path = null) : bool
    {
        // EntityFile information
        $entity_file_model = Dz::makeObject(EntityFile::class);
        $entity_file_model->relation_type = $attribute;

        // Upload file via service
        $upload_file_service = Dz::makeObject(UploadFileService::class, [$model, $this, $entity_file_model, $destination_path]);
        return $upload_file_service->run();
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->file_name;
    }
}
