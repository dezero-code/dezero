<?php
/**
 * Language model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\settings\models;

use dezero\behaviors\WeightBehavior;
use dezero\helpers\ArrayHelper;
use dezero\modules\settings\models\base\Language as BaseLanguage;
use dezero\modules\settings\models\query\LanguageQuery;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Language model class for table "language".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property string $language_id
 * @property string $name
 * @property string $native
 * @property string $prefix
 * @property int $is_ltr_direction
 * @property int $is_default
 * @property int $weight
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
 * @property User $createdUser
 * @property User $disabledUser
 * @property User $updatedUser
 * @property Category[] $categories
 * @property PimProductComment[] $pimProductComments
 * @property PimProductSearch[] $pimProductSearches
 * @property PimTranslatedProduct[] $pimTranslatedProducts
 * @property User[] $users
 */
class Language extends BaseLanguage
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['language_id', 'name', 'prefix'], 'required'],
            'integerFields' => [['is_ltr_direction', 'is_default', 'weight', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],

            // Max length rules
            'max6' => [['language_id'], 'string', 'max' => 6],
            'max16' => [['prefix'], 'string', 'max' => 16],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max64' => [['name', 'native'], 'string', 'max' => 64],

            // Default NULL
            'defaultNull' => [['native', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],

            // UNIQUE rules
            'languageIdUnique' => [['language_id'], 'unique'],
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
                [
                    // Weight
                    'class' => WeightBehavior::class,
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
            'language_id' => Yii::t('backend', 'Language ID'),
            'name' => Yii::t('backend', 'Name'),
            'native' => Yii::t('backend', 'Native'),
            'prefix' => Yii::t('backend', 'Prefix'),
            'is_ltr_direction' => Yii::t('backend', 'Is Ltr Direction'),
            'is_default' => Yii::t('backend', 'Is Default'),
            'weight' => Yii::t('backend', 'Weight'),
            'disabled_date' => Yii::t('backend', 'Disabled Date'),
            'disabled_user_id' => Yii::t('backend', 'Disabled User ID'),
            'created_date' => Yii::t('backend', 'Created Date'),
            'created_user_id' => Yii::t('backend', 'Created User ID'),
            'updated_date' => Yii::t('backend', 'Updated Date'),
            'updated_user_id' => Yii::t('backend', 'Updated User ID'),
            'entity_uuid' => Yii::t('backend', 'Entity Uuid'),
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
    public function getDisabledUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'disabled_user_id']);
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
        return $this->native;
    }
}
