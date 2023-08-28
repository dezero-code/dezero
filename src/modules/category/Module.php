<?php
/**
 * Module for CATEGORY administration
 */

namespace dezero\modules\category;

use dezero\modules\category\assets\CategoryAsset;
use Yii;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'category'  => \dezero\modules\category\controllers\CategoryController::class,
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
        CategoryAsset::register(Yii::$app->view);

        parent::init();
    }
}
