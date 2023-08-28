<?php
/**
 * Module for MYMODULE administration
 */

namespace dezero\modules\mymodule;

use dezero\modules\mymodule\assets\MymoduleAsset;
use Yii;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'mymodule'  => \dezero\modules\mymodule\controllers\MymoduleController::class,
    ];



    /**
     * Initializes the module.
     *
     * This method is called after the module is created and initialized with property values
     * given in configuration. The default implementation will initialize [[controllerNamespace]]
     * if it is not set.
     */
    public function init()
    {
        // Register Javascript & CSS files for this module
        MymoduleAsset::register(Yii::$app->view);

        parent::init();
    }
}
