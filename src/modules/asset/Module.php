<?php
/**
 * Module for asset files management
 */

namespace dezero\modules\asset;

use dezero\modules\asset\assets\AssetAsset;
use Yii;

class Module extends \dezero\base\Module
{
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
        AssetAsset::register(Yii::$app->view);

        parent::init();
    }
}
