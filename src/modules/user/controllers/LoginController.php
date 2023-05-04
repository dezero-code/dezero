<?php
/**
 * LoginController class for backend
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\forms\LoginForm;
use dezero\modules\user\models\User;
use Dz;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
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

        $form = Dz::makeObject(LoginForm::class);

        // Form validation via AJAX
        if ( Yii::$app->request->isAjax && $form->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $errors = ActiveForm::validate($form);
            if ( ! empty($errors) )
            {
                // $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $event);
            }

            return $errors;
        }

        if ( $form->load(Yii::$app->request->post()) && $form->login() )
        {
            return $this->goBack();
        }

        $form->password = '';

        return $this->render('//user/account/login', [
            'model' => $form
        ]);
    }
}
