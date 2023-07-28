<?php
/*
|--------------------------------------------------------------------------
| Use case "Deletes an mymodule"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\mymodule\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\modules\mymodule\events\MymoduleEvent;
use dezero\modules\mymodule\models\Mymodule;
use dezero\traits\ErrorTrait;
use dezero\traits\FlashMessageTrait;
use Yii;

class MymoduleDeleteService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(Mymodule $mymodule_model)
    {
        $this->mymodule_model = $mymodule_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Delete the Mymodule model
        if ( ! $this->deleteMymodule() )
        {
            return false;
        }

        return true;
    }


    /**
     * Delete the Mymodule model
     */
    private function deleteMymodule() : bool
    {
        // Custom event triggered on "beforeDelete"
        $mymodule_event = Dz::makeObject(MymoduleEvent::class, [$this->mymodule_model]);
        $this->mymodule_model->trigger(MymoduleEvent::EVENT_BEFORE_DELETE, $mymodule_event);

        if ( $this->mymodule_model->delete() )
        {
            // Custom event triggered on "afterDelete"
            $this->mymodule_model->trigger(MymoduleEvent::EVENT_AFTER_DELETE, $mymodule_event);

            return true;
        }

        // Error message
        $this->addError(Yii::t('backend', 'Mymodule could not be DELETED.'));

        return false;
    }
}
