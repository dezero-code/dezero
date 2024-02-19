<?php
/*
|-----------------------------------------------------------------
| Controller class for testing log component
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\web\Controller;
use dezero\helpers\Log;
use Yii;

class LogController extends Controller
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
        // Testing logs
        $this->testLogs();

        return $this->render('//test/test/index');
    }


    /**
     * Testing logs
     */
    private function testLogs()
    {
        \DzLog::dev(["What", "are", "you", "doing"]);

        // Yii component
        // Yii::error('Default error');
        // Yii::warning('Default warning');
        // Yii::info('Default info');
        // Yii::debug('Default debug');
        // Yii::debug(['Dev message', 'Another message'], 'dev');
    }
}
