<?php
/*
|--------------------------------------------------------------------------
| Use case "Create a new category"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\category\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\asset\models\AssetImage;
use dezero\modules\category\events\CategoryEvent;
use dezero\modules\category\models\Category;
use dezero\traits\ErrorTrait;
use Yii;

class CategoryCreateService implements ServiceInterface
{
    use ErrorTrait;


    /**
     * Constructor
     */
    public function __construct(Category $category_model, ?Category $category_parent_model, ?AssetImage $asset_image_model)
    {
        $this->category_model = $category_model;
        $this->category_parent_model = $category_parent_model;
        $this->asset_image_model = $asset_image_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Check parent category
        $this->checkParent();

        // Create new Category model
        if ( ! $this->saveCategory() )
        {
            // Upload TEMP image (category isn't yet created)
            $this->uploadTempImage();

            return false;
        }

        // Upload image (after category has been created)
        $this->uploadImage();

        return true;
    }


    /**
     * Check parent category
     */
    private function checkParent() : void
    {
        if ( $this->category_parent_model !== null )
        {
            $this->category_model->category_parent_id = $this->category_parent_model->category_id;
            $this->category_model->category_type = $this->category_parent_model->category_type;
            $this->category_model->depth = $this->category_parent_model->depth + 1;
        }
    }


    /**
     * Create new Category model
     */
    private function saveCategory() : bool
    {
        // Custom event triggered on "beforeCreate"
        $category_event = Dz::makeObject(CategoryEvent::class, [$this->category_model]);
        $this->category_model->trigger(CategoryEvent::EVENT_BEFORE_CREATE, $category_event);

        // Validate model's attributes
        if ( ! $this->category_model->validate() )
        {
            $this->addError($this->category_model->getErrors());

            return false;
        }

        // Save the model
        $this->category_model->save(false);

        // Custom event triggered on "afterCreate"
        $this->category_model->trigger(CategoryEvent::EVENT_AFTER_CREATE, $category_event);

        return true;
    }


    /**
     * Upload TEMP image (category isn't yet created)
     */
    private function uploadTempImage() : void
    {
        // Uploads a new file
        if ( $this->asset_image_model !== null && $this->asset_image_model->uploadTempFile($this->category_model, 'image_file_id') )
        {
            $this->category_model->image_file_id = $this->asset_image_model->file_id;
        }
    }


    /**
     * Upload image (after category has been created)
     */
    private function uploadImage() : void
    {
        // Uploads a new file
        if ( $this->asset_image_model !== null && $this->asset_image_model->uploadFile($this->category_model, 'image_file_id', $this->category_model->imageDirectory()) )
        {
            $this->category_model->image_file_id = $this->asset_image_model->file_id;
        }
    }
}
