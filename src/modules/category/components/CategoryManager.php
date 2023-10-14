<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Db;
use dezero\modules\category\models\Category;
use Yii;
use yii\base\Component;

/**
 * CategoryManager - Helper classes collection for Category model
 */
class CategoryManager extends Component
{
    /**
     * Return an array with all the children for a Category model
     */
    public function getAllChildren(int $category_id) : array
    {
        $vec_output = [];

        $vec_category_children_models = Category::find()
            ->category_parent($category_id)
            ->all();

        if ( !empty($vec_category_children_models) )
        {
            foreach ( $vec_category_children_models as $category_children_model )
            {
                $vec_output[] = $category_children_model;

                // Find grandchildren
                $vec_category_grandchildren_models = $this->getAllChildren($category_children_model->category_id);
                if ( ! empty($vec_category_grandchildren_models) )
                {
                    $vec_output = ArrayHelper::merge($vec_output, $vec_category_grandchildren_models);
                }
            }
        }

        return $vec_output;
    }


    /**
     * Get Category models given a depth level
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


    /**
     * Category list (usually used on a SELECT2 dropdown)
     */
    public function getCategoryList($category_type) : array
    {
        $vec_output = [];

        $vec_category_models = Category::find()
            ->category_type($category_type)
            ->enabled()
            ->orderBy([
                'depth'     => SORT_ASC,
                'weight'    => SORT_ASC
            ])
            ->all();

        if ( !empty($vec_category_models) )
        {
            foreach ( $vec_category_models as $category_model )
            {
                $vec_output[$category_model->category_id] = $category_model->fullTitle();
            }
        }

        return $vec_output;
    }
}
