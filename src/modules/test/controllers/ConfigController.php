<?php
/*
|-----------------------------------------------------------------
| Controller class for testing config component class
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use Yii;

class FileController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action
     */
    public function actionIndex()
    {
        // Testing config
        $this->testConfig();

        return $this->render('//test/test/index');
    }


    /**
     * Testing configuration component
     */
    private function testConfig()
    {
        d(Yii::$app->config->get('images'));
        d(Yii::$app->config->get('common/aliases'));
        d(Yii::$app->config->get('common/aliases', '@vendor'));
        d(Yii::$app->config->getDb());

        dd("----------- FINISHED TESTS -----------");
    }
}
