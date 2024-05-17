<?php
/**
 * Currency model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models;

use dezero\helpers\ArrayHelper;
use dezero\modules\settings\models\base\Currency as BaseCurrency;
use dezero\modules\settings\models\query\CurrencyQuery;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Currency model class for table "currency".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property string $currency_code
 * @property string $name
 * @property string $symbol
 * @property int $numeric_code
 * @property string $format
 * @property string $minor_unit
 * @property string $major_unit
 * @property string $thousands_separator
 * @property string $decimal_separator
 * @property int $decimals_number
 * @property int $weight
 * @property int $is_default
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
 */
class Currency extends BaseCurrency
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['currency_code', 'name', 'symbol', 'numeric_code', 'format'], 'required'],
            'integerFields' => [['numeric_code', 'decimals_number', 'weight', 'is_default', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],

            // Max length rules
            'max3' => [['currency_code'], 'string', 'max' => 3],
            'max8' => [['symbol'], 'string', 'max' => 8],
            'max16' => [['format', 'minor_unit', 'major_unit', 'thousands_separator', 'decimal_separator'], 'string', 'max' => 16],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max64' => [['name'], 'string', 'max' => 64],

            // Default NULL
            'defaultNull' => [['minor_unit', 'major_unit', 'thousands_separator', 'decimal_separator', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],

            // UNIQUE rules
            'numericCodeUnique' => [['numeric_code'], 'unique'],
            'currencyCodeUnique' => [['currency_code'], 'unique'],
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
            'currency_code' => Yii::t('backend', 'Currency Code'),
            'name' => Yii::t('backend', 'Name'),
            'symbol' => Yii::t('backend', 'Symbol'),
            'numeric_code' => Yii::t('backend', 'Numeric Code'),
            'format' => Yii::t('backend', 'Format'),
            'minor_unit' => Yii::t('backend', 'Minor Unit'),
            'major_unit' => Yii::t('backend', 'Major Unit'),
            'thousands_separator' => Yii::t('backend', 'Thousands Separator'),
            'decimal_separator' => Yii::t('backend', 'Decimal Separator'),
            'decimals_number' => Yii::t('backend', 'Decimals Number'),
            'weight' => Yii::t('backend', 'Weight'),
            'is_default' => Yii::t('backend', 'Is Default'),
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
        return $this->currency_code;
    }
}
