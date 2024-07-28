<?php
/*
|-----------------------------------------------------------------
| Controller class for testing Redis
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\web\Controller;
use Yii;

class RedisController extends Controller
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
        Yii::$app->redis->set('testkey', 'Esto es una prueba para comprobar que Redis funciona correctamente');
        dd(Yii::$app->redis->get('testkey'));

        return $this->render('//test/test/index');
    }
}
