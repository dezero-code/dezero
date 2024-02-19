<?php
/*
|-----------------------------------------------------------------
| Controller class for testing session component
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\web\Controller;
use Dz;
use Yii;

class SessionController extends Controller
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
        // Testing sessions
        $this->testSession();

        return $this->render('//test/test/index');
    }


    /**
     * Testing session component
     */
    private function testSession()
    {
        $session = Yii::$app->session;
        $session->set('language', 'en-ES');
        Log::dev($session->has('language'));
        Log::dev($session->get('language'));

        dd("----------- FINISHED TESTS -----------");
    }
}
