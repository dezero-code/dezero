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
    public function __construct(Category $category_model, ?string $status_change)
    {
        $this->category_model = $category_model;
        $this->status_change = !empty($status_change) ? $status_change : null;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Save current Category model
        if ( ! $this->saveCategory() )
        {
            return false;
        }

        // Status change action? Enable, disable or delete
        $this->applyStatusChange();

        return true;
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
    private function applyStatusChange() : void
    {
        if ( $this->status_change !== null )
        {
            switch ( $this->status_change )
            {
                case 'disable':
                    $this->disable();
                break;

                case 'enable':
                    $this->enable();
                break;

                case 'delete':
                    $this->delete();
                break;
            }
        }
    }


    /**
     * Disables OrganizerCategory model
     */
    private function disable()
    {
        if ( $this->category_model->disable() )
        {
            $this->addFlashMessage('Category DISABLED successfully');
        }
        else
        {
            $this->addError('Category could not be DISABLED');
        }
    }


    /**
     * Enables OrganizerCategory model
     */
    private function enable()
    {
        if ( $this->category_model->enable() )
        {
            $this->addFlashMessage('Category ENABLED successfully');
        }
        else
        {
            $this->addError('Category could not be ENABLED');
        }
    }


    /**
     * Deletes OrganizerCategory model
     */
    private function delete()
    {
        if ( $this->category_model->delete() )
        {
            $this->addFlashMessage('Category DELETED successfully');
        }
        else
        {
            $this->addError('Category could not be DELETED');
        }
    }
}
