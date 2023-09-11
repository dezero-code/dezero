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

        $data_provider = $category_search_model->search(Yii::$app->request->get());

        return $this->render($category_search_model->viewPath('index'),[
            'data_provider'         => $data_provider,
            'category_search_model' => $category_search_model,
            'vec_config'            => $category_search_model->getConfig()
        ]);
    }


    /**
     * Create action for Category model
     */
    public function actionCreate()
    {
        $this->requireLogin();
        $this->requirePermission('category_manage');

        $category_model = Dz::makeObject(Category::class);
        $asset_image_model = Dz::makeObject(AssetImage::class);

        // Validate model via AJAX
        $this->validateAjaxRequest($category_model);

        // Form submitted
        if ( $category_model->load(Yii::$app->request->post()) )
        {
            // Create category via CategoryCreateService class
            $category_create_service = Dz::makeObject(CategoryCreateService::class, [$category_model, $asset_image_model]);
            if ( $category_create_service->run() )
            {
                Yii::$app->session->setFlash('success', Yii::t('category', 'Category created succesfully'));

                return $this->redirect(["/category/{$category_model->category_type}/update", 'category_id' => $category_model->category_id]);
            }
            else
            {
                $category_create_service->showErrors();
            }
        }

        return $this->render($category_model->viewPath('create'), [
            'category_model'    => $category_model,
        ]);
    }


    /**
     * Update action for Category model
     */
    public function actionUpdate($category_id)
    {
        $this->requireLogin();
        $this->requirePermission('category_manage');

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
                // Success message & redirect
                Yii::$app->session->setFlash('success', Yii::t('category', 'Category updated succesfully'));

                return $this->redirect(["/category/{$category_model->category_type}/update", 'category_id' => $category_model->category_id]);

            }
            else
            {
                $category_update_service->showErrors();
            }
        }

        return $this->render($category_model->viewPath('update'), [
            'category_model'    => $category_model
        ]);
    }


    /**
     * Delete action for Category model
     */
    public function actionDelete($category_id)
    {
        $this->requireLogin();
        $this->requirePermission('category_manage');
        $this->requirePostRequest();

        // Load Category model
        $category_model = Dz::loadModel(Category::class, $category_id);

        // Delete category via CategoryDeleteService class
        $category_delete_service = Dz::makeObject(CategoryDeleteService::class, [$category_model]);
        if ( $category_delete_service->run() )
        {
            Yii::$app->session->setFlash('success', Yii::t('category', 'Category has been deleted.'));
        }
        else
        {
            $category_delete_service->showErrors();
        }

        return $this->redirect(["/category/{$category_model->category_type}"]);
    }


    /**
     * Return the category type
     */
    abstract protected function getCategoryType() : string;
}
