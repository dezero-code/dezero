<?php
/*
|--------------------------------------------------------------------------
| Use case "Create a new user"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\user\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\user\events\UserEvent;
use dezero\modules\user\models\User;
use dezero\traits\ErrorTrait;
use Yii;

class UserCreateService implements ServiceInterface
{
    use ErrorTrait;


    /**
     * Password without encryption
     */
    private $original_password;


    /**
     * Constructor
     */
    public function __construct(User $user_model, array $vec_roles)
    {
        $this->user_model = $user_model;
        $this->vec_roles = $vec_roles;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Validate email address
        if ( ! $this->validateEmail() )
        {
            return false;
        }

        // Create new User model
        if ( ! $this->saveUser() )
        {
            return false;
        }

        // Add roles to new user
        $this->addRoles();


        // Send a welcome email
        // $this->send_welcome_email();

        return true;
    }


    /**
     * Email address validation
     */
    private function validateEmail() : bool
    {
        // Check if email address is valid
        if ( ! StringHelper::validateEmail($this->user_model->email) )
        {
            return false;
        }

        // Check if email belongs to another user or it is valid
        $existing_user_model = User::find()
            ->email($this->user_model->email)
            ->one();

        if ( $existing_user_model )
        {
            $this->user_model->addError('email', 'This email address belongs to another user');

            return false;
        }

        return true;
    }


    /**
     * Create new User model
     */
    private function saveUser() : bool
    {
        // Random code for auth token
        $auth_token = Yii::$app->userManager->generateAuthToken();

        // Assign attributes
        $this->user_model->setAttributes([
            // 'username'          => Yii::app()->organizerManager->organizer_prefix . $sync_code,
            // 'auth_token'        => StringHelper::encrypt(microtime() . $auth_token),
            'auth_token'        => $auth_token,
            'status_type'       => 'active',
            'default_role'      => ( !empty($this->vec_roles) && isset($this->vec_roles[0]) ) ? $this->vec_roles[0] : null
        ]);

        // Save original password and encrypt it
        $this->original_password = $this->user_model->password;
        $this->user_model->password = Yii::$app->security->generatePasswordHash($this->user_model->password);

        // Custom event triggered on "beforeCreate"
        $user_event = Dz::makeObject(UserEvent::class, [$this->user_model]);
        $this->user_model->trigger(UserEvent::EVENT_BEFORE_CREATE, $user_event);

        // Validate model's attributes
        if ( ! $this->user_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->user_model->save(false);

        // Custom event triggered on "afterCreate"
        $this->user_model->trigger(UserEvent::EVENT_AFTER_CREATE, $user_event);

        return true;
    }


    /**
     * Add roles to new user
     */
    private function addRoles() : void
    {
        if ( !empty($this->vec_roles) )
        {
            foreach ( $this->vec_roles as $role_name )
            {
                $this->user_model->assignRole($role_name);
                // AuthHelper::assignRole($role_name, $this->user_model->user_id);
            }
        }
    }


    /**
     * Send a welcome email with original password
     */
    /*
    private function send_welcome_email()
    {
        // Add original password into    mail tokens
        Yii::app()->mail->addTokens([
            '{password}'  => $this->original_password
        ]);

        return $this->user_model->send_email('welcome_email');
    }
    */
}
