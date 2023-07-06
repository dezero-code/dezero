<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\helpers\AuthHelper;
use dezero\modules\user\models\User;
use dezero\modules\user\models\search\UserSearch;
use dezero\modules\user\services\UserCreateService;
use dezero\modules\user\services\UserUpdateService;
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

        // Validate model via AJAX
        $this->validateAjaxRequest($user_model);

        // Form submitted
        if ( $user_model->load(Yii::$app->request->post()) )
        {
            // Create user via UserCreateService class
            $user_create_service = Yii::createObject(UserCreateService::class, [$user_model, $vec_assigned_roles]);
            if ( $user_create_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('user', 'User created succesfully'));
                $this->redirect(['/user/admin/update', 'user_id' => $user_model->id]);
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

        // Load User model
        $user_model = Dz::loadModel(User::class, $user_id);

        // Password has changed?
        $is_password_changed = Yii::$app->request->post('IsPasswordChanged', false);
        if ( ! $is_password_changed )
        {
            Yii::$app->request->removeBodyParam('User', 'password');
            Yii::$app->request->removeBodyParam('User', 'verify_password');
        }

        // Validate model via AJAX
        $this->validateAjaxRequest($user_model);

        // Assigned roles
        $vec_assigned_roles = $user_model->getRoles();
        if ( !empty($vec_assigned_roles) )
        {
            $vec_assigned_roles = array_keys($user_model->getRoles());
        }

         // Form submitted
        if ( $user_model->load(Yii::$app->request->post()) )
        {
            // Assinged roles
            $vec_assigned_roles = Yii::$app->request->post('UserRoles', []);

            // Enable, disable or delete action?
            $status_change = Yii::$app->request->post('StatusChange', null);

            // Update user via UserUpdateService class
            $user_update_service = Yii::createObject(UserUpdateService::class, [$user_model, $vec_assigned_roles, $is_password_changed, $status_change]);
            if ( $user_update_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('user', 'User updated succesfully'));
                $this->redirect(['/user/admin/update', 'user_id' => $user_model->id]);
            }
        }

        // Clean password
        $user_model->password = '';
        $user_model->verify_password = '';

        return $this->render('//user/admin/update', [
            'user_model'            => $user_model,
            'vec_roles'             => AuthHelper::getRolesList(),
            'vec_assigned_roles'    => $vec_assigned_roles,
        ]);
    }
}
