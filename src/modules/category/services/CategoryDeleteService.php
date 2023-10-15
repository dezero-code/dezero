<?php
/*
|--------------------------------------------------------------------------
| Use case "Deletes an category"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\category\services;

use Dz;
use dezero\contracts\ServiceInterface;
use dezero\modules\category\events\CategoryEvent;
use dezero\modules\category\models\Category;
use dezero\traits\ErrorTrait;
use dezero\traits\FlashMessageTrait;
use Yii;

class CategoryDeleteService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(Category $category_model)
    {
        $this->category_model = $category_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Delete the Category model
        if ( ! $this->deleteCategory() )
        {
            return false;
        }

        return true;
    }


    /**
     * Delete the Category model
     */
    private function deleteCategory() : bool
    {
        // Custom event triggered on "beforeDelete"
        $category_event = Dz::makeObject(CategoryEvent::class, [$this->category_model]);
        $this->category_model->trigger(CategoryEvent::EVENT_BEFORE_DELETE, $category_event);

        if ( $this->category_model->delete() !== false )
        {
            // Custom event triggered on "afterDelete"
            $this->category_model->trigger(CategoryEvent::EVENT_AFTER_DELETE, $category_event);

            return true;
        }

        // Error message
        $this->addError(Yii::t('backend', 'Category could not be DELETED.'));

        return false;
    }
}
