<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\modules\category\models\Category;
use Yii;
use yii\base\Component;

/**
 * CategoryManager - Helper classes collection for category theme
 */
class CategoryManager extends Component
{
    /**
     * Get Category models from LEVEL 1
     */
    public function getAllByDepth(string $category_type, int $depth = 0) : ?array
    {
        return Category::find()
            ->category_type($category_type)
            ->depth($depth)
            ->orderBy(['weight' => SORT_ASC])
            ->all();
    }
}
