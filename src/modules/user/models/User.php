<?php
/**
 * User model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\user\models;

use dezero\helpers\ArrayHelper;
use dezero\modules\auth\rbac\AuthTrait;
use dezero\modules\settings\models\Language;
use dezero\modules\user\models\query\UserQuery;
use dezero\modules\user\models\base\User as BaseUser;
use yii\base\NotSupportedException;
use yii\db\ActiveQueryInterface;
use yii\web\IdentityInterface;
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
 * @property string $auth_token
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
class User extends BaseUser implements IdentityInterface
{
    use AuthTrait;

    public const STATUS_TYPE_ACTIVE = 'active';
    public const STATUS_TYPE_DISABLED = 'disabled';
    public const STATUS_TYPE_BANNED = 'banned';
    public const STATUS_TYPE_PENDING = 'pending';
    public const STATUS_TYPE_DELETED = 'deleted';


    /**
     * @var Verify password
     */
    public $verify_password;

    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['username', 'email', 'password', 'auth_token'], 'required'],
            'integerFields' => [['last_login_date', 'is_verified_email', 'last_verification_date', 'is_force_change_password', 'last_change_password_date', 'is_superadmin', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],

            // Max length rules
            'max6' => [['language_id'], 'string', 'max' => 6],
            'max16' => [['default_theme'], 'string', 'max' => 16],
            'max32' => [['auth_token'], 'string', 'max' => 32],
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
                'default0' => [['is_verified_email', 'is_force_change_password'], 'default', 'value' => 0],
                'defaultStatus' => [['status_type'], 'default', 'value' => self::STATUS_TYPE_ACTIVE],
                'defaultLanguage' => [['is_force_change_password'], 'default', 'value' => 'es-ES'],
                'defaultTimezone' => [['timezone'], 'default', 'value' => 'Europe/Madrid'],
                'passwordLength' => ['password', 'string', 'min' => 6, 'max' => 60],

                // Verify Password
                'verifyPassword' => [['verify_password'], 'string'],
                'requiredVerifyPassword' => [['verify_password'], 'required', 'on' => ['insert', 'change_password']],
                'comparePasswords' => [
                    ['verify_password'], 'compare', 'compareAttribute' => 'password',
                    'message' => Yii::t('backend', 'Passwords don\'t match'),
                    'on' => ['insert', 'change_password'],
                ],
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
            'user_id' => Yii::t('user', 'ID'),
            'username' => Yii::t('user', 'Username'),
            'email' => Yii::t('user', 'Email'),
            'password' => Yii::t('user', 'Password'),
            'auth_token' => Yii::t('user', 'Auth Token'),
            'first_name' => Yii::t('user', 'First Name'),
            'last_name' => Yii::t('user', 'Last Name'),
            'status_type' => Yii::t('user', 'Status'),
            'language_id' => Yii::t('user', 'Language'),
            'last_login_date' => Yii::t('user', 'Last Login'),
            'last_login_ip' => Yii::t('user', 'Last Login Ip'),
            'is_verified_email' => Yii::t('user', 'Is Verified Email'),
            'last_verification_date' => Yii::t('user', 'Last Verification Date'),
            'is_force_change_password' => Yii::t('user', 'Force Change Password?'),
            'last_change_password_date' => Yii::t('user', 'Last Change Password'),
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

            // Custom labels
            'verify_password' => Yii::t('user', 'Repeat Password'),
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
    public function getLanguage() : ActiveQueryInterface
    {
        return $this->hasOne(Language::class, ['language_id' => 'language_id']);
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
    | ENUM LABELS
    |--------------------------------------------------------------------------
    */

    /**
     * Get "status_type" labels
     */
    public function status_type_labels() : array
    {
        return Yii::$app->userManager->statusLabels();
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
     * Get "status_type" colors
     */
    public function status_type_colors() : array
    {
        return Yii::$app->userManager->statusColors();
    }


    /*
    |--------------------------------------------------------------------------
    | IdentifyInterface METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }


    /**
     * {@inheritdoc}
     *
     * @throws NotSupportedException
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        throw new NotSupportedException('Method "' . __CLASS__ . '::' . __METHOD__ . '" is not implemented.');
    }


    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAttribute('auth_token') === $authKey;
    }


    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getAttribute('user_id');
    }


    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->getAttribute('auth_token');
    }


    /**
     * Check if this User model can perform the operation as specified by the given permission
     *
     * Is similar to Yii::$app->user->can(), but with this method you can check permissions for any user
     */
    public function can($permission_name)
    {
        return Yii::$app->authManager->checkAccess($this->user_id, $permission_name);
    }


    /**
     * Check if current user belongs to Admin role and has been marked as SUPERADMIN
     */
    public function isSuperadmin() : bool
    {
        return $this->is_superadmin == 1;
    }


    /*
    |--------------------------------------------------------------------------
    | STATUS TYPE METHODS
    |--------------------------------------------------------------------------
    */

    public function isActive() : bool
    {
        return $this->isEnabled();
    }

    public function isBanned() : bool
    {
        return $this->status_type === self::STATUS_TYPE_BANNED;
    }

    public function isPending() : bool
    {
        return $this->status_type === self::STATUS_TYPE_PENDING;
    }

    public function isDeleted() : bool
    {
        return $this->status_type === self::STATUS_TYPE_DELETED;
    }


    /**
     * Check if User model is enabled
     */
    public function isEnabled() : bool
    {
        return $this->status_type === self::STATUS_TYPE_ACTIVE && parent::isEnabled();
    }


    /**
     * Eanbles User model
     */
    public function enable()
    {
        // $this->status_type = self::STATUS_TYPE_ACTIVE;
        if ( ! $this->changeStatus(self::STATUS_TYPE_ACTIVE) )
        {
            return false;
        }

        return parent::enable();
    }


    /**
     * Check if User model is disabled
     */
    public function isDisabled() : bool
    {
        return $this->status_type === self::STATUS_TYPE_DISABLED && parent::isDisabled();
    }


    /**
     * Disables User model
     */
    public function disable()
    {
        // $this->status_type = self::STATUS_TYPE_DISABLED;
        if ( ! $this->changeStatus(self::STATUS_TYPE_DISABLED) )
        {
            return false;
        }

        return parent::disable();
    }


    /**
     * Check if User model is forced to change the password
     */
    public function isForceChangePassword() : bool
    {
        return $this->is_force_change_password === 1;
    }



    /*
    |--------------------------------------------------------------------------
    | PASSWORD METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Change password
     */
    public function changePassword(string $new_password, bool $is_update_force_change_password = true) : bool
    {
        $this->password = Yii::$app->security->generatePasswordHash($new_password);
        $this->auth_token = Yii::$app->userManager->generateAuthToken();
        $this->last_change_password_date = time();

        // Update force change password?
        if ( $is_update_force_change_password && $this->is_force_change_password == 1 )
        {
            $this->is_force_change_password = 0;
        }

        return true;
    }


    /*
    |--------------------------------------------------------------------------
    | TITLE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Get full name: first name + last name
     */
    public function fullName() : string
    {
        $full_name = $this->first_name;
        if ( $this->last_name )
        {
            $full_name .= ' '. $this->last_name;
        }

        return $full_name;
    }


    /**
     * Title used for this model
     */
    public function title() : string
    {
        $full_name = $this->fullName();

        return !empty($full_name) ? $full_name : $this->username;
    }
}
