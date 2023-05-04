<?php
/**
 * LoginForm model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\forms;

use dezero\helpers\SecurityHelper;
use dezero\modules\user\models\User;
use yii\base\Model;
use Yii;
use yii\base\InvalidParamException;
use yii\web\IdentityInterface;


/**
 * Login form
 */
class LoginForm extends Model
{
    /**
     * @var string login User's email or username
     */
    public $username;


    /**
     * @var string User's password
     */
    public $password;


    /**
     * @var bool whether to remember User's login
     */
    public $rememberMe = false;


    /**
     * @var User
     */
    protected $user;


    /**
     * @var SecurityHelper
     */
    protected $securityHelper;


    /**
     * Constructor
     */
    public function __construct(SecurityHelper $securityHelper, array $vec_config = [])
    {
        $this->securityHelper = $securityHelper;
        parent::__construct($vec_config);
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            'requiredFields' => [['username', 'password'], 'required'],
            'usernameTrim' => ['username', 'trim'],
            'rememberMe' => ['rememberMe', 'boolean'],
            'passwordValidate' => ['password', 'validatePassword'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'username' => Yii::t('user', 'Login'),
            'password' => Yii::t('user', 'Password'),
            'rememberMe' => Yii::t('user', 'Remember me next time'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function beforeValidate()
    {
        if ( ! parent::beforeValidate() )
        {
            return false;
        }

        $this->user = User::find()->usernameOrEmail(trim($this->username))->one();

        return true;
    }


    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array  $params    the additional name-value pairs given in the rule
     */
    public function validatePassword($attribute, $params)
    {
        if ( ! $this->hasErrors() )
        {
            if ( $this->user === null || ! $this->securityHelper->validatePassword($this->password, $this->user->password_hash) )
            {
                $this->addError($attribute, Yii::t('user', 'Invalid login or password'));
            }
        }
    }


    /**
     * Logs in a user using the provided username and password.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() : bool
    {
        if ( $this->validate() )
        {
            // $duration = $this->rememberMe ? $this->module->rememberLoginLifespan : 0;
            $duration = $this->rememberMe ? 3600 * 24 * 30 : 0;

            return Yii::$app->getUser()->login($this->user, $duration);
        }

        return false;
    }


    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @param  IdentityInterface $user
     * @return User
     */
    public function setUser(IdentityInterface $user)
    {
        return $this->user = $user;
    }
}
