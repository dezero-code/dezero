<?php
/*
|--------------------------------------------------------------------------
| Use case "Create a new mymodule"
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
use Yii;

class MymoduleCreateService implements ServiceInterface
{
    use ErrorTrait;


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
        // Create new Mymodule model
        if ( ! $this->saveMymodule() )
        {
            return false;
        }

        return true;
    }


    /**
     * Create new Mymodule model
     */
    private function saveMymodule() : bool
    {
        // Custom event triggered on "beforeCreate"
        $mymodule_event = Dz::makeObject(MymoduleEvent::class, [$this->mymodule_model]);
        $this->mymodule_model->trigger(MymoduleEvent::EVENT_BEFORE_CREATE, $mymodule_event);

        // Validate model's attributes
        if ( ! $this->mymodule_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->mymodule_model->save(false);

        // Custom event triggered on "afterCreate"
        $this->mymodule_model->trigger(MymoduleEvent::EVENT_AFTER_CREATE, $mymodule_event);

        return true;
    }
}
