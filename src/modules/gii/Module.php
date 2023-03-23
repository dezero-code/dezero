<?php
/**
 * This is the main module class for the Gii module.
 */

namespace dezero\modules\gii;

use Yii;

class Module extends \yii\gii\Module
{
    /**
     * This method is called when the module is being created
     * you may place code here to customize the module or the application
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
