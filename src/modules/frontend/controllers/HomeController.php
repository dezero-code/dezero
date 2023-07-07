<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\frontend\controllers;

use dezero\web\Controller;
use Yii;

class HomeController extends Controller
{
    /**
     * Home page
     *
     * THIS PAGE IS USUALLY CALLED AFTER LOGIN
     *
     * @see dezero\modules\user\Module.php (redirectAfterLogin)
     */
    public function actionIndex()
    {
        if ( ! Yii::$app->user->isGuest )
        {
            return $this->redirect(['/user/admin']);
        }

        return $this->redirect(['/user/login']);
    }
}
