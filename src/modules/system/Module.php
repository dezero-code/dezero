<?php
/**
 * Module for system administration tasks
 */

namespace dezero\modules\system;

use dezero\modules\system\assets\SystemAsset;
use yii\web\YiiAsset;
use Yii;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'backup'    => \dezero\modules\system\controllers\BackupController::class,
        'log'       => \dezero\modules\system\controllers\LogController::class,
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
        SystemAsset::register(Yii::$app->view);
        YiiAsset::register(Yii::$app->view);

        parent::init();
    }
}
