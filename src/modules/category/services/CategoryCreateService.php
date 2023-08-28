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
    public function __construct(Category $category_model)
    {
        $this->category_model = $category_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Create new Category model
        if ( ! $this->saveCategory() )
        {
            return false;
        }

        return true;
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
            return false;
        }

        // Save the model
        $this->category_model->save(false);

        // Custom event triggered on "afterCreate"
        $this->category_model->trigger(CategoryEvent::EVENT_AFTER_CREATE, $category_event);

        return true;
    }
}
