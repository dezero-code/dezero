<?php
/**
 * Base Country model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models\base;

use dezero\modules\settings\models\query\CountryQuery;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * DO NOT MODIFY THIS FILE! It is automatically generated by Gii.
 * If any changes are necessary, you must set or override the required
 * property or method in class "dezero\modules\settings\models\Country".
 *
 * Base Country model class for table "country".
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
 */
abstract class Country extends \dezero\entity\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName() : string
    {
        return 'country';
    }


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
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
    }


    /**
     * @return CountryQuery The ActiveQuery class for this model
     */
    public static function find() : CountryQuery
    {
        return new CountryQuery(static::class);
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->country_code;
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
    public function getDisabledUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'disabled_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     *
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::class, ['user_id' => 'updated_user_id']);
    }

*/
