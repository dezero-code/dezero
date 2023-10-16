<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\controllers;

use dezero\helpers\AuthHelper;
use dezero\modules\asset\models\AssetImage;
use dezero\modules\category\models\Category;
use dezero\modules\category\models\search\CategorySearch;
use dezero\modules\category\services\CategoryCreateService;
use dezero\modules\category\services\CategoryDeleteService;
use dezero\modules\category\services\CategoryUpdateService;
use dezero\web\Controller;
use yii\web\Response;
use Dz;
use Yii;


/**
 * Base controller class for Category model
 *
 * Common class methods used in different controllers of category module
 */
abstract class BaseCategoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireLogin();
        $this->requirePermission('category_manage');

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * List action for Category models
     */
    public function actionIndex()
    {
        $category_search_model = Dz::makeObject(CategorySearch::class);
        $category_search_model->category_type = $this->getCategoryType();

        // Filter by only the first level
        $category_search_model->category_parent_id = null;

        $data_provider = $category_search_model->search(Yii::$app->request->get());

        return $this->render($category_search_model->config->viewPath('index'),[
            'data_provider'         => $data_provider,
            'category_search_model' => $category_search_model
        ]);
    }


    /**
     * Create action for Category model
     */
    public function actionCreate()
    {
        $category_model = Dz::makeObject(Category::class);
        $category_model->category_type = $this->getCategoryType();
        $asset_image_model = Dz::makeObject(AssetImage::class);

        // Check if parent has been defined
        $category_parent_model = null;
        if ( !empty(Yii::$app->request->get('parent_id')) )
        {
            $category_parent_model = Dz::loadModel(Category::class, Yii::$app->request->get('parent_id'));
            if ( $category_parent_model )
            {
                $category_model->category_parent_id = $category_parent_model->category_id;
            }
        }

        // Validate model via AJAX
        $this->validateAjaxRequest($category_model);

        // Form submitted
        if ( $category_model->load(Yii::$app->request->post()) )
        {
            // Create category via CategoryCreateService class
            $category_create_service = Dz::makeObject(CategoryCreateService::class, [$category_model, $category_parent_model, $asset_image_model]);
            if ( $category_create_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('backend', $category_model->config->text('created_success')));

                // Redirect to update page
                return $this->redirect(["/category/{$category_model->category_type}/update", 'category_id' => $category_model->category_id]);
            }
            else
            {
                // Show error messages
                $category_create_service->showErrors();
            }
        }

        return $this->render($category_model->config->viewPath('create'), [
            'category_model'        => $category_model,
            'category_parent_model' => $category_parent_model
        ]);
    }


    /**
     * Update action for Category model
     */
    public function actionUpdate($category_id)
    {
        // Load Category model
        $category_model = Dz::loadModel(Category::class, $category_id);

        // AssetImage model
        $asset_image_model = $category_model->imageFile;
        if ( ! $asset_image_model )
        {
            $asset_image_model = Dz::makeObject(AssetImage::class);
        }
        // $asset_image_model->generatePreset('large');
        // $asset_image_model->generatePresets(['small', 'medium');
        // $asset_image_model->generateAllPresets();

        // Validate model via AJAX
        $this->validateAjaxRequest($category_model);

        // Form submitted
        if ( $category_model->load(Yii::$app->request->post()) )
        {
            // Enable, disable or delete action?
            $status_change = Yii::$app->request->post('StatusChange', null);

            // Update category via CategoryUpdateService class
            $category_update_service = Dz::makeObject(CategoryUpdateService::class, [$category_model, $asset_image_model, $status_change]);
            if ( $category_update_service->run() )
            {
                // Show flash messages if disable, enable or delete actions has been executed
                $category_update_service->showFlashMessages();

                // Delete action? Redirect to list page
                if ( $status_change === 'delete' )
                {
                    return $this->redirect(["/category/{$category_model->category_type}"]);
                }

                // Refresh page
                return $this->redirect(["/category/{$category_model->category_type}/update", 'category_id' => $category_model->category_id]);
            }
            else
            {
                // Show error messages
                $category_update_service->showErrors();
            }
        }

        return $this->render($category_model->config->viewPath('update'), [
            'category_model'    => $category_model
        ]);
    }


    /**
     * Delete action for Category model
     */
    public function actionDelete($category_id)
    {
        // Delete action only allowed by POST requests
        $this->requirePostRequest();

        // Load Category model
        $category_model = Dz::loadModel(Category::class, $category_id);

        // Delete category via CategoryDeleteService class
        $category_delete_service = Dz::makeObject(CategoryDeleteService::class, [$category_model]);
        if ( $category_delete_service->run() )
        {
            Yii::$app->session->setFlash('success', Yii::t('backend', $category_model->config->text('delete_success')));
        }
        else
        {
            $category_delete_service->showErrors();
        }

        return $this->redirect(["/category/{$category_model->category_type}"]);
    }


    /**
     * Update category weights
     */
    public function actionWeight($category_id)
    {
        // Update action only allowed by POST requests
        $this->requirePostRequest();

        $vec_ajax_output = ['result' => -1];

        $vec_nestable = Yii::$app->request->post('nestable', []);

        if ( !empty($vec_nestable) && is_array($vec_nestable) )
        {
            // 1st level (no parents)
            if ( $category_id == 0 )
            {
                foreach ( $vec_nestable as $num_category => $que_category )
                {
                    if ( isset($que_category['id']) )
                    {
                        $new_weight = (int)$num_category+1;
                        Yii::$app->categoryManager->updateWeightById($new_weight, (int)$que_category['id']);
                    }
                }
            }

            // From 2nd level to Nth levels
            else if ( isset($vec_nestable[0]['id']) && $vec_nestable[0]['id'] == $category_id )
            {
                // Check if current category has subcategories (children)
                if ( isset($vec_nestable[0]['children']) && !empty($vec_nestable[0]['children']) )
                {
                    Yii::$app->categoryManager->updateChildrenWeights($vec_nestable[0]['children']);
                }
            }

            $vec_ajax_output = ['result' => 1];
        }

        // Return JSON and end application
        return $this->asJson($vec_ajax_output);
    }


    /**
     * {@inheritdoc}
     */
    public function redirect($url, $statusCode = 302) : Response
    {
        // Replace from "_" to "-"
        if ( $url !== null && is_array($url) && isset($url[0]) )
        {
            $url[0] = str_replace("_", "-", $url[0]);
        }

        return $this->response->redirect($url, $statusCode);
    }


    /**
     * Return the category type
     */
    abstract protected function getCategoryType() : string;
}
