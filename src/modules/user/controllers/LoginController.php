<?php
/**
 * LoginController class for backend
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\forms\LoginForm;
use dezero\modules\user\models\User;
use Dz;
use yii\web\Controller;
use Yii;

class LoginController extends Controller
{
    /**
     * Main action login
     */
    public function actionIndex()
    {
        if ( ! Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $model = Dz::makeObject(LoginForm::class);

        if ( $model->load(Yii::$app->request->post()) && $model->login() )
        {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('//user/account/login', [
            'model' => $model
        ]);
    }
}
