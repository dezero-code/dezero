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
use dezero\contracts\ConfigInterface;
use dezero\helpers\ArrayHelper;
use dezero\modules\asset\models\AssetImage;
use dezero\modules\category\components\CategoryConfigurator;
use dezero\modules\category\models\query\CategoryQuery;
use dezero\modules\category\models\base\Category as BaseCategory;
use Dz;
use user\models\User;
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
 * @property User $createdUser
 * @property User $disabledUser
 * @property AssetImage $imageFile
 * @property Language $language
 * @property User $updatedUser
 * @property Category[] $categories
 */
class Category extends BaseCategory implements ConfigInterface
{
    /**
     * @var \dezero\modules\category\components\CategoryConfigurator
     */
    private $configurator;

    /**
     * @var int
     */
    private $total_subcategories;


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
            'category_id' => Yii::t('backend', 'Category ID'),
            'category_type' => Yii::t('backend', 'Category Type'),
            'category_parent_id' => Yii::t('backend', 'Category Parent ID'),
            'name' => Yii::t('backend', 'Name'),
            'description' => Yii::t('backend', 'Description'),
            'weight' => Yii::t('backend', 'Weight'),
            'depth' => Yii::t('backend', 'Depth'),
            'image_file_id' => Yii::t('backend', 'Image File ID'),
            'language_id' => Yii::t('backend', 'Language ID'),
            'category_translated_id' => Yii::t('backend', 'Category Translated ID'),
            'disabled_date' => Yii::t('backend', 'Disabled Date'),
            'disabled_user_id' => Yii::t('backend', 'Disabled User ID'),
            'created_date' => Yii::t('backend', 'Created Date'),
            'created_user_id' => Yii::t('backend', 'Created User ID'),
            'updated_date' => Yii::t('backend', 'Updated Date'),
            'updated_user_id' => Yii::t('backend', 'Updated User ID'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),

            'imageFile' => Yii::t('backend', 'Image'),
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
        return $this->hasOne(User::class, ['user_id' => 'created_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getDisabledUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'disabled_user_id']);
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
        return $this->hasOne(User::class, ['user_id' => 'updated_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getSubCategories() : ActiveQueryInterface
    {
        return $this->hasMany(Category::class, ['category_parent_id' => 'category_id'])
            ->orderBy(['weight' => SORT_ASC]);
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
    | SUBCATEGORIES
    |--------------------------------------------------------------------------
    */

    /**
     * Return total number of subcategories
     */
    public function getTotalSubcategories() : int
    {
        if ( $this->total_subcategories === null )
        {
            $this->total_subcategories = $this->getCategories()->count();
        }

        return $this->total_subcategories;
    }


    /**
     * Return all the parents
     */
    public function getAllParents() : array
    {
        return Yii::$app->categoryManager->getAllParents($this);
    }


    /**
     * Return all the children
     */
    public function getAllChildren() : array
    {
        return Yii::$app->categoryManager->getAllChildren($this->category_id);
    }


    /**
     * Check if a "category_id" is parent (father, grandfather, ...)
     */
    public function isParent(int $category_id) : bool
    {
        if ( $this->category_parent_id === $category_id )
        {
            return true;
        }

        return $this->categoryParent ? $this->categoryParent->isParent($category_id) : false;
    }


    /**
     * Return the first level Category model
     */
    public function getFirstLevelCategory() : self
    {
        if ( $this->categoryParent )
        {
            return $this->categoryParent->getFirstLevelCategory();
        }

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION BUILDER
    |--------------------------------------------------------------------------
    */

    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : CategoryConfigurator
    {
        if ( $this->configurator === null )
        {
            $this->configurator = Dz::makeObject(CategoryConfigurator::class, [$this, $this->category_type]);
        }

        return $this->configurator;
    }


    /**
     * Return max depth level
     */
    public function getMaxLevels() : int
    {
        return $this->config ? $this->config->getMaxLevels() : 1;
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
     * Full title with parents
     */
    public function fullTitle() : string
    {
        $title = "";
        if ( $this->categoryParent )
        {
            $title = $this->categoryParent->fullTitle();
        }

        if ( !empty($title) )
        {
            $title .= " - ";
        }

        return $title . $this->title();
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->name;
    }
}
