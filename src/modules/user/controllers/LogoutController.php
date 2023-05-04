<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\events\UserEvent;
use Dz;
use yii\web\Controller;
use Yii;


class LogoutController extends Controller
{
    /**
     * Main action for logout
     */
    public function actionIndex()
    {
        $user_event = Dz::makeObject(UserEvent::class, [Yii::$app->getUser()->getIdentity()]);

        // Custom event triggered on "before logout"
        $this->trigger(UserEvent::EVENT_BEFORE_LOGOUT, $user_event);

        if ( Yii::$app->getUser()->logout() )
        {
            // Custom event triggered on "after logout"
            $this->trigger(UserEvent::EVENT_AFTER_LOGOUT, $user_event);
        }

        return $this->goHome();
    }
}
