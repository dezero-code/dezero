<?php
/*
|--------------------------------------------------------------------------
| Service for use case "Updates an user"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\user\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\helpers\AuthHelper;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\user\events\UserEvent;
use dezero\modules\user\models\User;
use dezero\traits\ErrorTrait;
use Yii;

class UserUpdateService implements ServiceInterface
{
    use ErrorTrait;


    /**
     * Password without encryption
     */
    private $original_password;


    /**
     * Constructor
     */
    public function __construct(User $user_model, array $vec_roles, bool $is_password_changed, ?string $status_change)
    {
        $this->user_model = $user_model;
        $this->vec_roles = $vec_roles;
        $this->is_password_changed = $is_password_changed;
        $this->status_change = $status_change;
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

        // Update roles
        $this->updateRoles();


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
            ->where(['<>','user_id', $this->user_model->user_id])
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
        // Custom event triggered on "beforeUpdate"
        $user_event = Dz::makeObject(UserEvent::class, [$this->user_model]);
        $this->user_model->trigger(UserEvent::EVENT_BEFORE_UPDATE, $user_event);

        // Validate model's attributes
        if ( ! $this->user_model->validate() )
        {
            return false;
        }

        // Has password been changed? --> If yes, save original password and encrypt it
        if ( $this->is_password_changed && !empty($this->user_model->password) )
        {
            // Validate passwords
            $this->user_model->scenario = 'change_password';
            if ( ! $this->user_model->validate() )
            {
                return false;
            }

            // Change the password
            $this->original_password = $this->user_model->password;
            $this->user_model->changePassword($this->user_model->password, false);
        }

        // Save the model
        $this->user_model->save(false);

        // Custom event triggered on "afterUpdate"
        $this->user_model->trigger(UserEvent::EVENT_AFTER_UPDATE, $user_event);

        return true;
    }


    /**
     * Update roles for current user
     */
    private function updateRoles() : void
    {
        // Get current roles
        $vec_current_roles = $this->getCurrentRoles();

        // New roles?
        if ( !empty($this->vec_roles) )
        {
            foreach ( $this->vec_roles as $role_name )
            {
                if ( ! $this->user_model->hasRole($role_name) )
                {
                    $this->user_model->assignRole($role_name);
                }
            }
        }

        // Revoked roles?
        if ( !empty($vec_current_roles) )
        {
            foreach ( $vec_current_roles as $role_name )
            {
                if ( !in_array($role_name, $this->vec_roles) && $this->user_model->hasRole($role_name) )
                {
                    $this->user_model->removeRole($role_name);
                }
            }
        }
    }


    /**
     * Get current roles assigned to user before update
     */
    private function getCurrentRoles()
    {
        $vec_current_roles = $this->user_model->getRoles();
        if ( empty($vec_current_roles) )
        {
            return [];
        }

        return array_keys($vec_current_roles);
    }
}
