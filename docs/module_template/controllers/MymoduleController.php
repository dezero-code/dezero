<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\mymodule\controllers;

use dezero\helpers\AuthHelper;
use dezero\modules\mymodule\models\Mymodule;
use dezero\modules\mymodule\models\search\MymoduleSearch;
use dezero\modules\mymodule\services\MymoduleCreateService;
use dezero\modules\mymodule\services\MymoduleDeleteService;
use dezero\modules\mymodule\services\MymoduleUpdateService;
use dezero\web\Controller;
use Dz;
use Yii;

class MymoduleController extends Controller
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
     * List action for Mymodule models
     */
    public function actionIndex()
    {
        $mymodule_search_model = Dz::makeObject(MymoduleSearch::class);

        $data_provider = $mymodule_search_model->search(Yii::$app->request->get());

        return $this->render('//mymodule/mymodule/index',[
            'data_provider'         => $data_provider,
            'mymodule_search_model' => $mymodule_search_model
        ]);
    }


    /**
     * Create action for Mymodule model
     */
    public function actionCreate()
    {
        $this->requireLogin();
        $this->requirePermission('mymodule_manage');

        $mymodule_model = Dz::makeObject(Mymodule::class);

        // Validate model via AJAX
        $this->validateAjaxRequest($mymodule_model);

        // Form submitted
        if ( $mymodule_model->load(Yii::$app->request->post()) )
        {
            // Create mymodule via MymoduleCreateService class
            $mymodule_create_service = Dz::makeObject(MymoduleCreateService::class, [$mymodule_model]);
            if ( $mymodule_create_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('mymodule', 'Mymodule created succesfully'));
                return $this->redirect(['/mymodule/mymodule/update', 'mymodule_id' => $mymodule_model->mymodule_id]);
            }
            else
            {
                $mymodule_create_service->showErrors();
            }
        }

        return $this->render('//mymodule/mymodule/create', [
            'mymodule_model'    => $mymodule_model,
        ]);
    }


    /**
     * Update action for Mymodule model
     */
    public function actionUpdate($mymodule_id)
    {
        $this->requireLogin();
        $this->requirePermission('mymodule_manage');

        // Load Mymodule model
        $mymodule_model = Dz::loadModel(Mymodule::class, $mymodule_id);

        // Validate model via AJAX
        $this->validateAjaxRequest($mymodule_model);

         // Form submitted
        if ( $mymodule_model->load(Yii::$app->request->post()) )
        {
            // Enable, disable or delete action?
            $status_change = Yii::$app->request->post('StatusChange', null);

            // Update mymodule via MymoduleUpdateService class
            $mymodule_update_service = Dz::makeObject(MymoduleUpdateService::class, [$mymodule_model, $status_change]);
            if ( $mymodule_update_service->run() )
            {
                // Success message & redirect
                Yii::$app->session->setFlash('success', Yii::t('mymodule', 'Mymodule updated succesfully'));
                return $this->redirect(['/mymodule/mymodule/update', 'mymodule_id' => $mymodule_model->mymodule_id]);
            }
            else
            {
                $mymodule_update_service->showErrors();
            }
        }

        return $this->render('//mymodule/mymodule/update', [
            'mymodule_model'    => $mymodule_model,
        ]);
    }


    /**
     * Delete action for Mymodule model
     */
    public function actionDelete($mymodule_id)
    {
        $this->requireLogin();
        $this->requirePermission('mymodule_manage');
        $this->requirePostRequest();

        // Load Mymodule model
        $mymodule_model = Dz::loadModel(Mymodule::class, $mymodule_id);

        // Delete mymodule via MymoduleDeleteService class
        $mymodule_delete_service = Dz::makeObject(MymoduleDeleteService::class, [$mymodule_model]);
        if ( $mymodule_delete_service->run() )
        {
            Yii::$app->session->setFlash('success', Yii::t('mymodule', 'Mymodule has been deleted.'));
        }
        else
        {
            $mymodule_delete_service->showErrors();
        }

        return $this->redirect(['/mymodule/mymodule']);
    }
}
