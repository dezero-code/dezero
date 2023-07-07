<?php
/**
 * Module for user administation
 */

namespace dezero\modules\user;

use dezero\modules\user\assets\UserAsset;
use Yii;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'admin'     => \dezero\modules\user\controllers\AdminController::class,
        'login'     => \dezero\modules\user\controllers\LoginController::class,
        'logout'    => \dezero\modules\user\controllers\LogoutController::class,
    ];


    /**
     * @var array redirect URL after login
     */
    public $redirectAfterLogin = ['/'];


    /**
     * @var array redirect URL after logout
     */
    public $redirectAfterLogout = ['/'];


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
        UserAsset::register(Yii::$app->view);

        parent::init();
    }
}
