<?php
/**
 * User model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\models;

use dezero\helpers\ArrayHelper;
use dezero\modules\user\models\query\UserQuery;
use dezero\modules\user\models\base\User as BaseUser;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * User model class for table "user_user".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property int $user_id
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string $first_name
 * @property string $last_name
 * @property string $status_type
 * @property string $language_id
 * @property int $last_login_date
 * @property string $last_login_ip
 * @property int $is_verified_email
 * @property int $last_verification_date
 * @property int $is_force_change_password
 * @property int $last_change_password_date
 * @property string $default_role
 * @property string $default_theme
 * @property int $is_superadmin
 * @property string $timezone
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
 * @property Language $language
 * @property User $updatedUser
 * @property Language[] $languages
 * @property UserSession[] $userSessions
 * @property User[] $users
 */
class User extends BaseUser
{
    public const STATUS_TYPE_ACTIVE = 'active';
    public const STATUS_TYPE_DISABLED = 'disabled';
    public const STATUS_TYPE_BANNED = 'banned';
    public const STATUS_TYPE_PENDING = 'pending';
    public const STATUS_TYPE_DELETED = 'deleted';


    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['username', 'email', 'password', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'required'],
            'integerFields' => [['last_login_date', 'is_verified_email', 'last_verification_date', 'is_force_change_password', 'last_change_password_date', 'is_superadmin', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            
            // Max length rules
            'max6' => [['language_id'], 'string', 'max' => 6],
            'max16' => [['default_theme'], 'string', 'max' => 16],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max40' => [['timezone'], 'string', 'max' => 40],
            'max60' => [['password'], 'string', 'max' => 60],
            'max64' => [['default_role'], 'string', 'max' => 64],
            'max255' => [['username', 'email', 'first_name', 'last_name', 'last_login_ip'], 'string', 'max' => 255],
            
            // ENUM rules
            'statusTypeList' => ['status_type', 'in', 'range' => [
                    self::STATUS_TYPE_ACTIVE,
                    self::STATUS_TYPE_DISABLED,
                    self::STATUS_TYPE_BANNED,
                    self::STATUS_TYPE_PENDING,
                    self::STATUS_TYPE_DELETED,
                ]
            ],
            
            // Default NULL
            'defaultNull' => [['first_name', 'last_name', 'last_login_date', 'last_login_ip', 'last_verification_date', 'last_change_password_date', 'default_role', 'default_theme', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
            
            // UNIQUE rules
            'usernameUnique' => [['username'], 'unique'],
            'emailUnique' => [['email'], 'unique'],
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
            'user_id' => Yii::t('user', 'User ID'),
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'status_type' => Yii::t('user', 'Status Type'),
            'language_id' => Yii::t('user', 'Language ID'),
            'last_login_date' => Yii::t('user', 'Last Login Date'),
            'last_login_ip' => Yii::t('user', 'Last Login Ip'),
            'is_verified_email' => Yii::t('user', 'Is Verified Email'),
            'last_verification_date' => Yii::t('user', 'Last Verification Date'),
            'is_force_change_password' => Yii::t('user', 'Is Force Change Password'),
            'last_change_password_date' => Yii::t('user', 'Last Change Password Date'),
            'default_role' => Yii::t('user', 'Default Role'),
            'default_theme' => Yii::t('user', 'Default Theme'),
            'is_superadmin' => Yii::t('user', 'Is Superadmin'),
            'timezone' => Yii::t('user', 'Timezone'),
            'disabled_date' => Yii::t('user', 'Disabled Date'),
            'disabled_user_id' => Yii::t('user', 'Disabled User ID'),
            'created_date' => Yii::t('user', 'Created Date'),
            'created_user_id' => Yii::t('user', 'Created User ID'),
            'updated_date' => Yii::t('user', 'Updated Date'),
            'updated_user_id' => Yii::t('user', 'Updated User ID'),
            'entity_uuid' => Yii::t('user', 'Entity Uuid'),
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
        return $this->hasOne(User::className(), ['user_id' => 'created_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getDisabledUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::className(), ['user_id' => 'disabled_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getLanguage() : ActiveQueryInterface
    {
        return $this->hasOne(Language::className(), ['language_id' => 'language_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUpdatedUser() : ActiveQueryInterface
    {
        return $this->hasOne(User::className(), ['user_id' => 'updated_user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getLanguages() : ActiveQueryInterface
    {
        return $this->hasMany(Language::className(), ['created_user_id' => 'user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUserSessions() : ActiveQueryInterface
    {
        return $this->hasMany(UserSession::className(), ['user_id' => 'user_id']);
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getUsers() : ActiveQueryInterface
    {
        return $this->hasMany(User::className(), ['created_user_id' => 'user_id']);
    }


   /*
    |--------------------------------------------------------------------------
    | ENUM LABELS
    |--------------------------------------------------------------------------
    */

    /**
     * Get "status_type" labels
     */
    public function status_type_labels() : array
    {
        return [
            self::STATUS_TYPE_ACTIVE => Yii::t('user', 'Active'),
            self::STATUS_TYPE_DISABLED => Yii::t('user', 'Disabled'),
            self::STATUS_TYPE_BANNED => Yii::t('user', 'Banned'),
            self::STATUS_TYPE_PENDING => Yii::t('user', 'Pending'),
            self::STATUS_TYPE_DELETED => Yii::t('user', 'Deleted'),
        ];
    }


    /**
     * Get "status_type" specific label
     */
    public function status_type_label(?string $status_type = null) : string
    {
        $status_type = ( $status_type === null ) ? $this->status_type : $status_type;
        $vec_labels = $this->status_type_labels();

        return isset($vec_labels[$status_type]) ? $vec_labels[$status_type] : '';
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->username;
    }
}
