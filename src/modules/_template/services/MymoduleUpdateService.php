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
        $this->applyStatusChange();

        return true;
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
    private function applyStatusChange() : void
    {
        if ( $this->status_change !== null )
        {
            switch ( $this->status_change )
            {
                case 'disable':
                    $this->disable();
                break;

                case 'enable':
                    $this->enable();
                break;

                case 'delete':
                    $this->delete();
                break;
            }
        }
    }


    /**
     * Disables OrganizerMymodule model
     */
    private function disable()
    {
        if ( $this->mymodule_model->disable() )
        {
            $this->addFlashMessage('Mymodule DISABLED successfully');
        }
        else
        {
            $this->addError('Mymodule could not be DISABLED');
        }
    }


    /**
     * Enables OrganizerMymodule model
     */
    private function enable()
    {
        if ( $this->mymodule_model->enable() )
        {
            $this->addFlashMessage('Mymodule ENABLED successfully');
        }
        else
        {
            $this->addError('Mymodule could not be ENABLED');
        }
    }


    /**
     * Deletes OrganizerMymodule model
     */
    private function delete()
    {
        if ( $this->mymodule_model->delete() )
        {
            $this->addFlashMessage('Mymodule DELETED successfully');
        }
        else
        {
            $this->addError('Mymodule could not be DELETED');
        }
    }
}
