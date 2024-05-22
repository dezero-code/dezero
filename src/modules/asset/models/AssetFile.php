<?php
/**
 * AssetFile model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\asset\models;

use dezero\base\File;
use dezero\db\ActiveRecord as ActiveRecord;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Transliteration;
use dezero\helpers\Url;
use dezero\modules\asset\models\query\AssetFileQuery;
use dezero\modules\asset\models\base\AssetFile as BaseAssetFile;
use dezero\modules\asset\services\FileSaveService;
use dezero\modules\asset\services\UploadFileService;
use dezero\modules\asset\services\UploadFileTempService;
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
    // Asset types
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
     * @var array. Register uploads deleted
     */
    private $vec_upload_deleted = [];


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
            'file_id' => Yii::t('backend', 'File ID'),
            'file_name' => Yii::t('backend', 'File Name'),
            'file_path' => Yii::t('backend', 'File Path'),
            'file_mime' => Yii::t('backend', 'File Mime'),
            'file_size' => Yii::t('backend', 'File Size'),
            'file_options' => Yii::t('backend', 'File Options'),
            'asset_type' => Yii::t('backend', 'Asset Type'),
            'title' => Yii::t('backend', 'Title'),
            'description' => Yii::t('backend', 'Description'),
            'original_file_name' => Yii::t('backend', 'Original File Name'),
            'reference_entity_uuid' => Yii::t('backend', 'Reference Entity Uuid'),
            'reference_entity_type' => Yii::t('backend', 'Reference Entity Type'),
            'created_date' => Yii::t('backend', 'Created Date'),
            'created_user_id' => Yii::t('backend', 'Created User ID'),
            'updated_date' => Yii::t('backend', 'Updated Date'),
            'updated_user_id' => Yii::t('backend', 'Updated User ID'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
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
            self::ASSET_TYPE_IMAGE => Yii::t('backend', 'Image'),
            self::ASSET_TYPE_DOCUMENT => Yii::t('backend', 'Document'),
            self::ASSET_TYPE_VIDEO => Yii::t('backend', 'Video'),
            self::ASSET_TYPE_OTHER => Yii::t('backend', 'Other'),
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
    | ASSET TYPE METHODS
    |--------------------------------------------------------------------------
    */

    // public function isImage() : bool
    // {
    //     return $this->asset_type === self::ASSET_TYPE_IMAGE;
    // }

    public function isDocument() : bool
    {
        return $this->asset_type === self::ASSET_TYPE_DOCUMENT;
    }

    public function isVideo() : bool
    {
        return $this->asset_type === self::ASSET_TYPE_VIDEO;
    }

    public function isOther() : bool
    {
        return $this->asset_type === self::ASSET_TYPE_OTHER;
    }


    /**
     * Check if current file is image
     */
    public function isImage() : bool
    {
        if ( $this->loadFile() )
        {
            return $this->file->isImage();
        }

        return false;
    }


    /**
     * Check if current file is saved on a TEMP directory
     */
    public function isTemp() : bool
    {
        return preg_match("/^\@tmp|^\@privateTmp/", $this->file_path);
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


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getEntityFile() : ActiveQueryInterface
    {
        return $this->hasOne(EntityFile::class, ['file_id' => 'file_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getEntityFiles() : ActiveQueryInterface
    {
        return $this->hasMany(EntityFile::class, ['file_id' => 'file_id']);
    }


    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        // Image?
        if ( preg_match("/^image\//", $this->file_mime) )
        {
            $this->asset_type = self::ASSET_TYPE_IMAGE;
        }

        // Video?
        if ( preg_match("/^video\//", $this->file_mime) )
        {
            $this->asset_type = self::ASSET_TYPE_VIDEO;
        }

        return parent::beforeValidate();
    }


    /**
     * {@inheritdoc}
     */
    public function delete()
    {
        // Delete file
        $this->deleteFile();

        return parent::delete();
    }


    /*
    |--------------------------------------------------------------------------
    | FILE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Create a AssetFile model given a File object
     */
    public static function createFromFile(File $file) : self
    {
        $asset_file_model = Dz::makeObject(AssetFile::class);
        $asset_file_model->setAttributes([
            'file_name'             => Transliteration::file($file->basename()),
            'file_path'             => str_replace(Yii::getAlias('@webroot'), '@www', $file->dirname()) .'/',
            'file_mime'             => $file->mime(),
            'file_size'             => $file->size(),
            'asset_type'            => $file->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
            'original_file_name'    => Transliteration::file($file->basename()),
        ]);

        return $asset_file_model;
    }


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


    /**
     * Move a file to a new destination
     */
    public function move(string $destination_path) : bool
    {
        // Check if file exists and if destination path is different from current path
        if ( $this->loadFile() && $this->file_path !== $destination_path && $this->file->move($destination_path . $this->file_name) )
        {
            $this->file_path = $destination_path;
            $this->saveAttributes(['file_path' => $this->file_path]);

            return true;
        }

        return false;
    }


    /**
     * Delete file from filesystem
     */
    public function deleteFile() : bool
    {
        if ( $this->loadFile() )
        {
            return $this->file->delete();
        }

        return false;
    }


    /**
     * Return the size of current filesystem object in the given unit
     */
    public function formatSize() : string
    {
        if ( $this->loadFile() )
        {
            return $this->file->formatSize($this->file_size);
        }

        return "{$this->file_size} B";
    }


    /**
     * Return download URL
     */
    public function downloadUrl() : string
    {
        return Url::to('/asset/download', ['uuid' => $this->entity_uuid]);
    }


    /*
    |--------------------------------------------------------------------------
    | SAVE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Save AssetFile model from a file
     */
    public function saveFromFile(File $file, ActiveRecord $model, string $file_attribute) : bool
    {
        // Get attributes from given File
        $this->setAttributes([
            'file_name'             => Transliteration::file($file->basename()),
            'file_path'             => str_replace(Yii::getAlias('@webroot'), '@www', $file->dirname()) .'/',
            'file_mime'             => $file->mime(),
            'file_size'             => $file->size(),
            'asset_type'            => $file->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
            'original_file_name'    => Transliteration::file($file->basename()),
        ]);

        // EntityFile information
        $entity_file_model = null;
        $entity_file_model = $this->getEntityFile()->one();
        if ( $entity_file_model === null )
        {
            $entity_file_model = Dz::makeObject(EntityFile::class);
        }

        $entity_file_model->relation_type = $file_attribute;

        // Save file via SaveFileService
        return Dz::makeObject(FileSaveService::class, [$model, $file_attribute, $this, $entity_file_model])->run();
    }


    /*
    |--------------------------------------------------------------------------
    | UPLOAD METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Upload a file
     */
    public function uploadFile(ActiveRecord $model, string $file_attribute, ?string $destination_path = null, bool $is_multiple = false) : bool
    {
        // EntityFile information
        $entity_file_model = null;
        if ( ! $is_multiple )
        {
            $entity_file_model = $this->getEntityFile()->one();
        }
        if ( $entity_file_model === null )
        {
            $entity_file_model = Dz::makeObject(EntityFile::class);
        }
        $entity_file_model->relation_type = $file_attribute;

        // Upload file via UploadFileService
        $upload_file_service = Dz::makeObject(UploadFileService::class, [$model, $file_attribute, $this, $entity_file_model, $destination_path]);
        if ( ! $upload_file_service->run() )
        {
            // Upload file has been deleted?
            if ( $upload_file_service->last_action === 'delete' )
            {
                $this->addUploadDeleted($file_attribute);
            }

            return false;
        }

        return true;
    }


    /**
     * Upload a file into a TEMP directory
     */
    public function uploadTempFile(ActiveRecord $model, string $file_attribute, bool $is_multiple = false) : bool
    {
        // Upload file via UploadFileTempService
        $upload_file_temp_service = Dz::makeObject(UploadFileTempService::class, [$model, $file_attribute, $this]);
        if ( ! $upload_file_temp_service->run() )
        {
            // Upload file has been deleted?
            if ( $upload_file_temp_service->last_action === 'delete' )
            {
                $this->addUploadDeleted($file_attribute);
            }

            return false;
        }

        return true;
    }


    /**
     * Add a new upload deleted
     */
    public function addUploadDeleted($attribute) : void
    {
        $this->vec_upload_deleted[$attribute] = $attribute;
    }


    /**
     * Check if upload has been deleted
     */
    public function isUploadDeleted($attribute) : bool
    {
        return !empty($this->vec_upload_deleted) && isset($this->vec_upload_deleted[$attribute]);
    }



    /*
    |--------------------------------------------------------------------------
    | TITLE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Return file URL
     */
    public function url() : string
    {
        $url = str_replace(Yii::getAlias('@webroot'), '', Yii::getAlias($this->getRelativePath()));

        return Url::to($url);
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->file_name;
    }
}
