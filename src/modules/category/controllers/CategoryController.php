<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\controllers;

use dezero\modules\category\controllers\BaseCategoryController;
use Dz;
use Yii;

class CategoryController extends BaseCategoryController
{
    /**
     * Return the category type
     */
    protected function getCategoryType() : string
    {
        return 'category';
    }
}
