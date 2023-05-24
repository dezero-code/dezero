<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\events\UserEvent;
use dezero\modules\user\models\User;
use dezero\modules\user\models\search\UserSearch;
use dezero\web\Controller;
use Dz;
use Yii;

class AdminController extends Controller
{
    /**
     * List action for User models
     */
    public function actionIndex()
    {
        $user_search_model = Dz::makeObject(UserSearch::class);

        $data_provider = $user_search_model->search(Yii::$app->request->get());

        return $this->render('//user/admin/index',[
            'data_provider'     => $data_provider,
            'user_search_model' => $user_search_model
        ]);
    }


    /**
     * Create action for User model
     */
    public function actionCreate()
    {
        $this->requireLogin();
        $this->requirePermission('puser_manage');

        $user_model = Dz::makeObject(User::class);
        $user_event = Dz::makeObject(UserEvent::class, [$user_model]);

        // Validate model via AJAX
        $this->validateAjaxRequest($user_model);

         // Form submitted
        if ( $user_model->load(Yii::$app->request->post()) && $user_model->validate() )
        {
            // Custom event triggered on "before create"
            $this->trigger(UserEvent::EVENT_BEFORE_CREATE, $user_event);

            Yii::$app->session->setFlash('success', Yii::t('user', 'User created succesfully'));
            $this->redirect(['/admin/user/create']);
            // $this->redirect(['/admin/user/update', 'user_id' => $user_model->id]);
        }

        return $this->render('//user/admin/create', [
            'user_model' => $user_model
        ]);
    }
}
