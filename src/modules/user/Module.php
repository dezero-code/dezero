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
     * This method is called when the module is being created
     * you may place code here to customize the module or the application
     */
    public function init()
    {
        UserAsset::register(Yii::$app->view);

        parent::init();
    }
}
