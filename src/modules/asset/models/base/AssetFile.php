<?php
/**
 * Base AssetFile model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\asset\models\base;

use dezero\modules\asset\models\query\AssetFileQuery;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * DO NOT MODIFY THIS FILE! It is automatically generated by Gii.
 * If any changes are necessary, you must set or override the required
 * property or method in class "dezero\modules\asset\models\AssetFile".
 *
 * Base AssetFile model class for table "asset_file".
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
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
 * @property User $createdUser
 * @property User $updatedUser
 * @property Batch[] $batches
 * @property Category[] $categories
 * @property EntityFile[] $entityFiles
 */
abstract class AssetFile extends \dezero\entity\ActiveRecord
{
    public const ASSET_TYPE_IMAGE = 'image';
    public const ASSET_TYPE_DOCUMENT = 'document';
    public const ASSET_TYPE_VIDEO = 'video';
    public const ASSET_TYPE_OTHER = 'other';


    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'asset_file';
    }


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
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
    }


    /**
     * @return AssetFileQuery The ActiveQuery class for this model
     */
    public static function find() : AssetFileQuery
    {
        return new AssetFileQuery(static::class);
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->file_name;
    }
}

/**
 * These are relations and enum methods generated with Gii.
 * YOU CAN USE THESE METHODS IN THE PARENT MODEL CLASS
 *

   /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    *

    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getCreatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'created_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getBatches() : ActiveQueryInterface
    {
        return $this->hasMany(Batch::class, ['file_id' => 'file_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getCategories() : ActiveQueryInterface
    {
        return $this->hasMany(Category::class, ['image_file_id' => 'file_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getEntityFiles() : ActiveQueryInterface
    {
        return $this->hasMany(EntityFile::class, ['file_id' => 'file_id']);
    }


   /*
    |--------------------------------------------------------------------------
    | ENUM LABELS
    |--------------------------------------------------------------------------
    *

    /**
     * Get "asset_type" labels
     *
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
     *
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
    *

    public function isImage() : bool
    {
        return $this->asset_type === self::ASSET_TYPE_IMAGE;
    }

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

*/
