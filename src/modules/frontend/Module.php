<?php
/**
 * Module for frontend pages
 */

namespace dezero\modules\frontend;

class Module extends \dezero\base\Module
{
    /**
     * @var array mapping from controller ID to controller configurations.
     */
    public $controllerMap = [
        'home'     => \dezero\modules\frontend\controllers\HomeController::class,
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
        // Load FRONTEND theme
        $this->loadTheme('frontend');

        parent::init();
    }
}
