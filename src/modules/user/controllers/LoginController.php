<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\forms\LoginForm;
use dezero\modules\user\models\User;
use dezero\modules\user\events\FormEvent;
use dezero\modules\user\events\UserEvent;
use Dz;
use yii\base\Event;
use yii\web\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
use Yii;

class LoginController extends Controller
{
    /**
     * Main action for login
     */
    public function actionIndex()
    {
        if ( ! Yii::$app->user->isGuest)
        {
            return $this->goHome();
        }

        $login_form = Dz::makeObject(LoginForm::class);
        $form_event = Dz::makeObject(FormEvent::class, [$login_form]);

        // Form validation via AJAX
        if ( Yii::$app->request->isAjax && $login_form->load(Yii::$app->request->post()) )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;

            $errors = ActiveForm::validate($login_form);
            if ( ! empty($errors) )
            {
                // Custom event triggered on "failed login"
                // if ( $this->hasEventHandlers(FormEvent::EVENT_FAILED_LOGIN) )
                // {
                    $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $form_event);
                // }
            }

            return $errors;
        }

        if ( $login_form->load(Yii::$app->request->post()) )
        {
            // Custom event triggered on "before login"
            $this->trigger(FormEvent::EVENT_BEFORE_LOGIN, $form_event);

            if ( $login_form->login() )
            {
                // Custom event triggered on "after login"
                $this->trigger(FormEvent::EVENT_AFTER_LOGIN, $form_event);

                $this->redirect($this->module->redirectAfterLogin);
                // return $this->goBack();
            }

            // Custom event triggered on "failed login"
            $this->trigger(FormEvent::EVENT_FAILED_LOGIN, $form_event);
            // Event::trigger(FormEvent::class, FormEvent::EVENT_FAILED_LOGIN, $form_event);
        }

        $login_form->password = '';

        return $this->render('//user/account/login', [
            'model' => $login_form
        ]);
    }
}
