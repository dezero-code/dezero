<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\events\UserEvent;
use dezero\web\Controller;
use Dz;
use Yii;


class LogoutController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action for logout
     */
    public function actionIndex()
    {
        $user = Yii::$app->getUser()->getIdentity();
        if ( $user !== null )
        {
            $user_event = Dz::makeObject(UserEvent::class, [$user]);

            // Custom event triggered on "before logout"
            $this->trigger(UserEvent::EVENT_BEFORE_LOGOUT, $user_event);

            if ( Yii::$app->getUser()->logout() )
            {
                // Custom event triggered on "after logout"
                $this->trigger(UserEvent::EVENT_AFTER_LOGOUT, $user_event);
            }
        }

        return $this->redirect($this->module->redirectAfterLogout);
        // return $this->goHome();
    }
}
