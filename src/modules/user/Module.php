<?php
/**
 * Module for user administation
 */

namespace dezero\modules\user;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'login' => \dezero\modules\user\controllers\LoginController::class,
        'logout' => \dezero\modules\user\controllers\LogoutController::class,
    ];


    /**
     * This method is called when the module is being created
     * you may place code here to customize the module or the application
     */
    public function init()
    {
        parent::init();
    }
}
