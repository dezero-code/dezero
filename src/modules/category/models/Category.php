<?php
/**
 * Category model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\models;

use dezero\behaviors\WeightBehavior;
use dezero\helpers\ArrayHelper;
use dezero\modules\asset\models\AssetImage;
use dezero\modules\category\models\query\CategoryQuery;
use dezero\modules\category\models\base\Category as BaseCategory;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Category model class for table "category_category".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $category_id
 * @property string $category_type
 * @property int $category_parent_id
 * @property string $name
 * @property string $description
 * @property int $weight
 * @property int $depth
 * @property int $image_file_id
 * @property string $language_id
 * @property int $category_translated_id
 * @property int $disabled_date
 * @property int $disabled_user_id
 * @property int $created_date
 * @property int $created_user_id
 * @property int $updated_date
 * @property int $updated_user_id
 * @property string $entity_uuid
 *
 * -------------------------------------------------------------------------
 * RELATIONS
 * -------------------------------------------------------------------------
 * @property Category $categoryParent
 * @property Category $categoryTranslated
 * @property UserUser $createdUser
 * @property UserUser $disabledUser
 * @property AssetImage $imageFile
 * @property Language $language
 * @property UserUser $updatedUser
 * @property Category[] $categories
 */
class Category extends BaseCategory
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['name'], 'required'],
            'integerFields' => [['category_parent_id', 'weight', 'depth', 'image_file_id', 'category_translated_id', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'stringFields' => [['description'], 'string'],
            
            // Max length rules
            'max6' => [['language_id'], 'string', 'max' => 6],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['category_type'], 'string', 'max' => 128],
            'max255' => [['name'], 'string', 'max' => 255],
            
            // Default NULL
            'defaultNull' => [['category_parent_id', 'description', 'image_file_id', 'category_translated_id', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
        ];
        */

        return ArrayHelper::merge(
            parent::rules(),
            [
                // Custom validation rules
                // 'image' => [['imageFile'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, jpeg', 'on' => ['insert', 'update']],
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
                [
                    // Weight
                    'class' => WeightBehavior::class,
                    'vec_attributes' => ['category_parent_id', 'category_type']
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
            'category_id' => Yii::t('category', 'Category ID'),
            'category_type' => Yii::t('category', 'Category Type'),
            'category_parent_id' => Yii::t('category', 'Category Parent ID'),
            'name' => Yii::t('category', 'Name'),
            'description' => Yii::t('category', 'Description'),
            'weight' => Yii::t('category', 'Weight'),
            'depth' => Yii::t('category', 'Depth'),
            'image_file_id' => Yii::t('category', 'Image File ID'),
            'language_id' => Yii::t('category', 'Language ID'),
            'category_translated_id' => Yii::t('category', 'Category Translated ID'),
            'disabled_date' => Yii::t('category', 'Disabled Date'),
            'disabled_user_id' => Yii::t('category', 'Disabled User ID'),
            'created_date' => Yii::t('category', 'Created Date'),
            'created_user_id' => Yii::t('category', 'Created User ID'),
            'updated_date' => Yii::t('category', 'Updated Date'),
            'updated_user_id' => Yii::t('category', 'Updated User ID'),
            'entity_uuid' => Yii::t('category', 'Entity Uuid'),

            'imageFile' => Yii::t('category', 'Image'),
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
    public function getCategoryParent() : ActiveQueryInterface
    {
        return $this->hasOne(Category::class, ['category_id' => 'category_parent_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getCategoryTranslated() : ActiveQueryInterface
    {
        return $this->hasOne(Category::class, ['category_id' => 'category_translated_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getCreatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(UserUser::class, ['user_id' => 'created_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getDisabledUser() : ActiveQueryInterface
    {
        return $this->hasOne(UserUser::class, ['user_id' => 'disabled_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getImageFile() : ActiveQueryInterface
    {
        return $this->hasOne(AssetImage::class, ['file_id' => 'image_file_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getLanguage() : ActiveQueryInterface
    {
        return $this->hasOne(Language::class, ['language_id' => 'language_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(UserUser::class, ['user_id' => 'updated_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getCategories() : ActiveQueryInterface
    {
        return $this->hasMany(Category::class, ['category_parent_id' => 'category_id']);
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
        // Force checking category_parent_id (must be different from current category_id)
        if ( ! $this->isNewRecord && $this->category_parent_id === $this->category_id )
        {
            $this->category_parent_id = null;
        }

        // Get depth level
        $this->depth = $this->getDepthLevel();

        return parent::beforeValidate();
    }



    /**
     * Get depth level (tree level)
     */
    public function getDepthLevel()
    {
        $depth = $this->depth;
        if ( $this->categoryParent )
        {
            $depth = $this->categoryParent->depth + 1;
        }

        // Max lenght is 255
        if ( $depth > 255 )
        {
            $depth = 255;
        }

        return $depth;
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return the configuration options for current category type
     */
    public function getConfig(?string $category_type = null) : ?array
    {
        $category_type = ( $category_type !== null ) ? $category_type : $this->category_type;

        return Yii::$app->categoryManager->getConfig($category_type);
    }


    /**
     * Return the view file path for a category type
     */
    public function viewPath(string $view_file, ?string $category_type = null) : string
    {
        $category_type = ( $category_type !== null ) ? $category_type : $this->category_type;

        return Yii::$app->categoryManager->viewPath($view_file, $category_type);
    }


    /**
     * Return the corresponding text
     */
    public function text(string $text_key, ?string $category_type = null) : string
    {
        $category_type = ( $category_type !== null ) ? $category_type : $this->category_type;

        return Yii::$app->categoryManager->text($text_key, $category_type);
    }


    /*
    |--------------------------------------------------------------------------
    | INFORMATION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return image directory path
     */
    public function imageDirectory()
    {
        return '@www/files/category/'. $this->category_id .'/';
    }

    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->name;
    }
}
