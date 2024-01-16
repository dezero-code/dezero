<?php
/*
|--------------------------------------------------------------------------
| Use case "Updates an category"
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
use dezero\traits\FlashMessageTrait;
use Yii;

class CategoryUpdateService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(Category $category_model, ?AssetImage $asset_image_model, ?string $status_change)
    {
        $this->category_model = $category_model;
        $this->asset_image_model = !empty($asset_image_model) ? $asset_image_model : null;
        $this->status_change = !empty($status_change) ? $status_change : null;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Upload or delete image
        $this->uploadImage();

        // Save current Category model
        if ( ! $this->saveCategory() )
        {
            return false;
        }

        // Status change action? Enable, disable or delete
        return $this->applyStatusChange();
    }


    /**
     * Upload or delete image
     */
    private function uploadImage() : void
    {
        // Image is enabled for this category?
        if ( $this->category_model->config->isImage() === false )
        {
            $this->asset_image_model = null;
        }

        if ( $this->asset_image_model === null )
        {
            return ;
        }

        // Uploads a new file
        if ( $this->$this->asset_image_model->uploadFile($this->category_model, 'image_file_id', $this->category_model->imageDirectory()) )
        {
            $this->category_model->image_file_id = $this->asset_image_model->file_id;
        }

        // Deleted previous file
        else if ( $this->asset_image_model->isUploadDeleted('image_file_id') )
        {
            $this->category_model->image_file_id = null;
        }
    }



    /**
     * Save current Category model
     */
    private function saveCategory() : bool
    {
        // Custom event triggered on "beforeUpdate"
        $category_event = Dz::makeObject(CategoryEvent::class, [$this->category_model]);
        $this->category_model->trigger(CategoryEvent::EVENT_BEFORE_UPDATE, $category_event);

        // Validate model's attributes
        if ( ! $this->category_model->validate() )
        {
            $this->addError($this->category_model->getErrors());

            return false;
        }

        // Save the model
        $this->category_model->save(false);

        // Custom event triggered on "afterUpdate"
        $this->category_model->trigger(CategoryEvent::EVENT_AFTER_UPDATE, $category_event);

        return true;
    }


    /**
     * Status change action? Enable, disable or delete
     */
    private function applyStatusChange() : bool
    {
        switch ( $this->status_change )
        {
            // DISABLE action
            case 'disable':
                return $this->disable();
            break;

            // ENABLE action
            case 'enable':
                return $this->enable();
            break;

            // DELETE action
            case 'delete':
                return $this->delete();
            break;

            // SAVE action --> Show success message
            default:
                $this->addFlashMessage(Yii::t('backend', $this->category_model->config->text('updated_success')));

                return true;
            break;
        }
    }


    /**
     * Disables a Category model
     */
    private function disable() : bool
    {
        if ( $this->category_model->disable() )
        {
            $this->addFlashMessage(Yii::t('backend', $this->category_model->config->text('disable_success')));

            return true;
        }

        $this->addError(Yii::t('backend', $this->category_model->config->text('disable_error')));

        return false;
    }


    /**
     * Enables a Category model
     */
    private function enable() : bool
    {
        if ( $this->category_model->enable() )
        {
            $this->addFlashMessage(Yii::t('backend', $this->category_model->config->text('enable_success')));

            return true;
        }

        $this->addError(Yii::t('backend', $this->category_model->config->text('enable_error')));

        return false;
    }


    /**
     * Deletes a Category model
     */
    private function delete() : bool
    {
        if ( $this->category_model->delete() !== false )
        {
            $this->addFlashMessage(Yii::t('backend', $this->category_model->config->text('delete_success')));

            return true;
        }

        $this->addError(Yii::t('backend', $this->category_model->config->text('delete_error')));

        return false;
    }
}
