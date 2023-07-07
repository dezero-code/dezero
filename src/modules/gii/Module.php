<?php
/**
 * This is the main module class for the Gii module.
 */

namespace dezero\modules\gii;

use Yii;

class Module extends \yii\gii\Module
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
        parent::init();
    }


    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Change dezero template extension (tpl.php) to default extension (php)
        Yii::$app->view->defaultExtension = 'php';

        // Sets the directory that contains the view files.
        $this->setViewPath('@vendor/yiisoft/yii2-gii/src/views');

        return parent::beforeAction($action);
    }
}
