<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\helpers\Db;
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


    /**
     * Update weight by ID
     */
    public function updateWeightById(int $weight, int $category_id) : int
    {
        // UPDATE menu weight
        return Db::update(
            Category::tableName(),
            ['weight' => $weight],
            'category_id = :category_id',
            [':category_id' => $category_id]
        );
    }


    /**
     * Update children weights in a recursivelly way
     */
    public function updateChildrenWeights(array $vec_children = []) : void
    {
        if ( empty($vec_children) )
        {
            return;
        }

        foreach ( $vec_children as $num_category => $que_category )
        {
            if ( isset($que_category['id']) )
            {
                $subcategory_id = substr($que_category['id'], 2);

                // UPDATE category weight
                $new_weight = (int)$num_category+1;
                $this->updateWeightById($new_weight, $subcategory_id);
            }

            if ( isset($que_category['children']) && !empty($que_category['children']) )
            {
                $this->updateChildrenWeights($que_category['children']);
            }
        }
    }
}
