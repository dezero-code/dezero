<?php
/*
|--------------------------------------------------------------------------
| Special controller class (inherated from Yii2 structure)
|--------------------------------------------------------------------------
*/

namespace dezero\controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'actions' => ['error'],
                        'allow' => true,
                    ],
                ],
            ],
        ];
    }


    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => \yii\web\ErrorAction::class,
                'view' => '//layouts/error'
            ],
        ];
    }


    /**
     * This is the action to handle external exceptions.
     */
    /*
    public function actionError()
    {
        $exception = Yii::$app->errorHandler->exception;
        if ( $exception !== null )
        {
            return $this->render('//layouts/error', ['exception' => $exception]);
        }
    }
    */

    /**
     * This is the action to handle external exceptions.
     */
    /*
    public function actionError()
    {
        if ( $error = Yii::$app->errorHandler->error )
        {
            // Errors on REST API?
            if ( preg_match("/^api\/v/", Yii::$app->request->getPathInfo()) )
            {
                Yii::import('@core.components.DzRest.*');
                $controller = new DzRestController('api');
                $controller->actionError();
            }
            else
            {
                if ( Yii::$app->request->isAjaxRequest )
                {
                    echo $error['message'];
                }
                else
                {
                    $this->render('//layouts/error', $error);
                }
            }
        }
    }
    */
}
