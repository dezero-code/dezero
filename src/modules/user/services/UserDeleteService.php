<?php
/*
|--------------------------------------------------------------------------
| Use case "Deletes an user"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\user\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\modules\user\events\UserEvent;
use dezero\modules\user\models\User;
use dezero\traits\ErrorTrait;
use dezero\traits\FlashMessageTrait;
use Yii;

class UserDeleteService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(User $user_model)
    {
        $this->user_model = $user_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Validate if the user to be deleted isn't the own account
        if ( ! $this->validateOwnAccount() )
        {
            return false;
        }

        // Delete the User model
        if ( ! $this->deleteUser() )
        {
            return false;
        }

        return true;
    }


    /**
     * Validate if the user to be deleted isn't the own account
     */
    private function validateOwnAccount() : bool
    {
        // if ( $this->user_model->user_id != Yii::$app->user->id )
        if ( $this->user_model->user_id == Yii::$app->user->id )
        {
            return true;
        }

        $this->addError(Yii::t('backend', 'You can not remove your own account.'));

        return false;
    }


    /**
     * Delete the User model
     */
    private function deleteUser() : bool
    {
        // Custom event triggered on "beforeDelete"
        $user_event = Dz::makeObject(UserEvent::class, [$this->user_model]);
        $this->user_model->trigger(UserEvent::EVENT_BEFORE_DELETE, $user_event);

        if ( $this->user_model->delete() )
        {
            // Custom event triggered on "afterDelete"
            $this->user_model->trigger(UserEvent::EVENT_AFTER_DELETE, $user_event);

            return true;
        }

        // Error message
        $this->addError(Yii::t('backend', 'User could not be DELETED.'));

        return false;
    }
}
