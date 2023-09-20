<?php
/**
 * Module for REST API
 */

namespace dezero\modules\api;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'assets'   => \dezero\modules\api\controllers\AssetsController::class,
        'auth'     => \dezero\modules\api\controllers\AuthController::class,
        'users'     => \dezero\modules\api\controllers\UsersController::class
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
        parent::init();
    }
}
