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


    public $events = [
        // Called from global "Event::trigger(FormEvent::class, ...)"
        [
            'class'     => \dezero\modules\user\events\FormEvent::class,
            'event'     => \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
            'callback'  => [\dezero\modules\user\listeners\UserListener::class, 'onFailedLogin']
        ],

        // Called from a controller "$this->trigger(...) or Event::trigger(LoginController::class)"
        // [
        //     'class'     => \dezero\modules\user\controllers\LoginController::class,
        //     'event'     => \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
        //     'callback'  => [\dezero\modules\user\listeners\UserListener::class, 'onFailedLogin']
        // ],
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
