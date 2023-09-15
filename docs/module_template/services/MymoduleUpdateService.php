<?php
/*
|--------------------------------------------------------------------------
| Use case "Updates an mymodule"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\mymodule\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\mymodule\events\MymoduleEvent;
use dezero\modules\mymodule\models\Mymodule;
use dezero\traits\ErrorTrait;
use dezero\traits\FlashMessageTrait;
use Yii;

class MymoduleUpdateService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(Mymodule $mymodule_model, ?string $status_change)
    {
        $this->mymodule_model = $mymodule_model;
        $this->status_change = !empty($status_change) ? $status_change : null;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Save current Mymodule model
        if ( ! $this->saveMymodule() )
        {
            return false;
        }

        // Status change action? Enable, disable or delete
        return $this->applyStatusChange();
    }



    /**
     * Save current Mymodule model
     */
    private function saveMymodule() : bool
    {
        // Custom event triggered on "beforeUpdate"
        $mymodule_event = Dz::makeObject(MymoduleEvent::class, [$this->mymodule_model]);
        $this->mymodule_model->trigger(MymoduleEvent::EVENT_BEFORE_UPDATE, $mymodule_event);

        // Validate model's attributes
        if ( ! $this->mymodule_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->mymodule_model->save(false);

        // Custom event triggered on "afterUpdate"
        $this->mymodule_model->trigger(MymoduleEvent::EVENT_AFTER_UPDATE, $mymodule_event);

        return true;
    }


    /**
     * Status change action? Enable, disable or delete
     */
    private function applyStatusChange() : bool
    {
        switch ( $this->status_change )
        {
            // DISABLE action
            case 'disable':
                return $this->disable();
            break;

            // ENABLE action
            case 'enable':
                return $this->enable();
            break;

            // DELETE action
            case 'delete':
                return $this->delete();
            break;

            // SAVE action --> Show success message
            default:
                $this->addFlashMessage(Yii::t('backend', 'Mymodule updated succesfully'));

                return true;
            break;
        }
    }


    /**
     * Disables a Mymodule model
     */
    private function disable() : bool
    {
        if ( $this->mymodule_model->disable() )
        {
            $this->addFlashMessage(Yii::t('backend', 'Mymodule DISABLED successfully'));

            return true;
        }

        $this->addError(Yii::t('backend', 'Mymodule could not be DISABLED'));

        return false;
    }


    /**
     * Enables a Mymodule model
     */
    private function enable() : bool
    {
        if ( $this->mymodule_model->enable() )
        {
            $this->addFlashMessage(Yii::t('backend', 'Mymodule ENABLED successfully'));

            return true;
        }

        $this->addError(Yii::t('backend', 'Mymodule could not be ENABLED'));

        return false;
    }


    /**
     * Deletes a Mymodule model
     */
    private function delete() : bool
    {
        if ( $this->mymodule_model->delete() !== false )
        {
            $this->addFlashMessage(Yii::t('backend', 'Mymodule DELETED successfully'));

            return true;
        }

        $this->addError(Yii::t('backend', 'Mymodule could not be DELETED'));

        return false;
    }
}
