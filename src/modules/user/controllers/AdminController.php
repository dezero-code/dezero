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
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


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
                return $this->redirect(['/user/admin/update', 'user_id' => $user_model->id]);
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
                return $this->redirect(['/user/admin/update', 'user_id' => $user_model->id]);
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


    /**
     * Delete action for User model
     */
    public function actionDelete($user_id)
    {
        $this->requireLogin();
        $this->requirePermission('user_manage');
        $this->requirePostRequest();

        Yii::$app->session->setFlash('success', Yii::t('backend', 'We have arrive here.'));
        return $this->redirect(['/user/admin']);

        // Load User model
        $user_model = Dz::loadModel(User::class, $user_id);

         if ( $user_id == Yii::$app->user->id )
         {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'You can not remove your own account.'));
        }
        else
        {
            $user_model->delete();

            Yii::$app->session->setFlash('success', Yii::t('backend', 'User has been deleted.'));
        }

        return $this->redirect(['/user/admin']);
    }


    /**
     * Change Status action for the SlidePanel widget
     */
    public function actionStatus($user_id)
    {
        $this->requireLogin();
        $this->requirePermission('user_manage');

        // Load User model
        $user_model = Dz::loadModel(User::class, $user_id);

        // Submitted form?
        if ( Yii::$app->request->isPost )
        {
            $vec_ajax_output = [
                'error_msg'     => '',
                'error_code'    => 0,
            ];

            $vec_input = $this->jsonInput();

            // #1 - JSON input params are correct?
            if ( !empty($vec_input) && isset($vec_input['user_id']) && isset($vec_input['new_status']) )
            {
                // #2 - User exists?
                if ( $user_model->user_id != $vec_input['user_id'] )
                {
                    $vec_ajax_output['error_code'] = 102;
                    $vec_ajax_output['error_msg'] = 'Denied acces - User #'. $vec_input['user_id'] .' does not exist';
                }
                else
                {
                    // #3 - Check "is_sending_mail" value
                    /*
                    $is_sending_mail = false;
                    if ( isset($vec_input['is_sending_mail']) && ($vec_input['is_sending_mail'] == "1" || $vec_input['is_sending_mail'] == 1) )
                    {
                        $is_sending_mail = true;
                    }
                    */

                    // #4 - Save new status
                    $comments = isset($vec_input['new_comments']) ? $vec_input['new_comments'] : null;
                    if ( ! $user_model->changeStatus($vec_input['new_status'], $comments, true) )
                    {
                        $vec_ajax_output['error_code'] = 103;
                        $vec_ajax_output['error_msg'] = 'Error - Status could not be changed';
                    }
                }
            }
            else
            {
                $vec_ajax_output['error_code'] = 201;
                $vec_ajax_output['error_msg'] = 'Access denied - JSON input params are incorrect';
            }

            // Return JSON and end application
            return $this->asJson($vec_ajax_output);
        }
        else
        {
            // By default, we'll send an email to the user
            // $user_model->is_sending_mail = 1;
        }

        // If we arrive here (not POST params), render partial view
        return $this->renderPartial('//entity/status/_slidepanel_status', [
            'model'         => $user_model,
            'buttonOptions' => [
                'id'        => 'user-status-save-btn',
                'data-user' => $user_model->user_id
            ]
        ]);
    }
}
