<?php
/**
 * Event listeners configuration
 */
return [
    // Called globally via "Event::trigger(FormEvent::class, ...)"
    // [
    //     'class'     => \dezero\modules\user\events\FormEvent::class,
    //     'event'     => \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
    //     'callback'  => [\dezero\modules\user\events\FormEvent::class, 'onFailedLogin']
    // ],

    // Called from LoginController via "$this->trigger(...) or Event::trigger(LoginController::class)"
    // [
    //     'class'     => \dezero\modules\user\controllers\LoginController::class,
    //     'event'     => \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
    //     'callback'  => [\dezero\modules\user\events\FormEvent::class, 'failedLogin']
    // ],

    // Called from LoginController via "$this->trigger(...) or Event::trigger(LoginController::class)"
    [
        'class'     => \dezero\modules\user\controllers\LoginController::class,
        'event'     => \dezero\modules\user\events\FormEvent::EVENT_AFTER_LOGIN,
        'callback'  => [\dezero\modules\user\events\FormEvent::class, 'afterLogin']
    ],
];
