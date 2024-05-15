<?php
/**
 * Country model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models;

use dezero\helpers\ArrayHelper;
use dezero\modules\settings\models\base\Country as BaseCountry;
use dezero\modules\settings\models\query\CountryQuery;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Country model class for table "country".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property string $country_code
 * @property string $alpha3_code
 * @property string $name
 * @property string $name_es
 * @property int $is_eu
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
 * @property Agency[] $agencies
 * @property EventConcert[] $eventConcerts
 */
class Country extends BaseCountry
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['country_code', 'alpha3_code', 'name', 'name_es'], 'required'],
            'integerFields' => [['is_eu', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],

            // Max length rules
            'max2' => [['country_code'], 'string', 'max' => 2],
            'max3' => [['alpha3_code'], 'string', 'max' => 3],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max64' => [['name', 'name_es'], 'string', 'max' => 64],

            // Default NULL
            'defaultNull' => [['disabled_date', 'disabled_user_id'], 'default', 'value' => null],

            // UNIQUE rules
            'alpha3CodeUnique' => [['alpha3_code'], 'unique'],
            'countryCodeUnique' => [['country_code'], 'unique'],
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
            'country_code' => Yii::t('backend', 'Country Code'),
            'alpha3_code' => Yii::t('backend', 'Alpha3 Code'),
            'name' => Yii::t('backend', 'Name'),
            'name_es' => Yii::t('backend', 'Name Es'),
            'is_eu' => Yii::t('backend', 'Is Eu'),
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
        return $this->country_code;
    }
}
