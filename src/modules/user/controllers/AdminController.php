<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\helpers\AuthHelper;
use dezero\modules\user\events\UserEvent;
use dezero\modules\user\models\User;
use dezero\modules\user\models\search\UserSearch;
use dezero\modules\user\services\UserCreateService;
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
        $this->requirePermission('user_manage');

        $user_model = Dz::makeObject(User::class);
        $vec_assigned_roles = Yii::$app->request->post('UserRoles', []);
        // $user_event = Dz::makeObject(UserEvent::class, [$user_model]);

        // Validate model via AJAX
        $this->validateAjaxRequest($user_model);


        // Form submitted
        // if ( $user_model->load(Yii::$app->request->post()) && $user_model->validate() )
        if ( $user_model->load(Yii::$app->request->post()) )
        {
            // Custom event triggered on "before create"
            // $this->trigger(UserEvent::EVENT_BEFORE_CREATE, $user_event);

            // Create OrganizerUser via UserCreateService class
            $user_create_service = Yii::createObject(UserCreateService::class, [$user_model, $vec_assigned_roles]);
            if ( $user_create_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('user', 'User created succesfully'));
                $this->redirect(['/admin/user/update', 'user_id' => $user_model->id]);
            }
        }

        return $this->render('//user/admin/create', [
            'user_model'            => $user_model,
            'vec_roles'             => AuthHelper::getRolesList(),
            'vec_assigned_roles'    => $vec_assigned_roles
        ]);
    }


    /**
     * Update action for User model
     */
    public function actionUpdate($user_id)
    {

        $this->requireLogin();
        $this->requirePermission('user_manage');

        $user_model = Dz::loadModel(User::class, $user_id);
        $user_event = Dz::makeObject(UserEvent::class, [$user_model]);

        // Validate model via AJAX
        $this->validateAjaxRequest($user_model);

         // Form submitted
        if ( $user_model->load(Yii::$app->request->post()) && $user_model->validate() )
        {
            // Custom event triggered on "before update"
            $this->trigger(UserEvent::EVENT_BEFORE_UPDATE, $user_event);

            Yii::$app->session->setFlash('success', Yii::t('user', 'User updated succesfully'));
            $this->redirect(['/admin/user/update', 'user_id' => $user_model->id]);
        }

        return $this->render('//user/admin/update', [
            'user_model' => $user_model
        ]);
    }
}
