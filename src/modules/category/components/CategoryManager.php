<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\components;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Db;
use dezero\helpers\StringHelper;
use dezero\modules\category\models\Category;
use Yii;
use yii\base\Component;

/**
 * CategoryManager - Helper classes collection for Category model
 */
class CategoryManager extends Component
{
    /**
     * Return an array if Category models filtered by "category_type"
     */
    public function getCategoriesByType(string $category_type, array $vec_options = []) : array
    {
        $category_query = Category::find()
            ->category_type($category_type)
            ->enabled()
            ->orderBy([
                'depth'     => SORT_ASC,
                'weight'    => SORT_ASC
            ]);

        // Filter by depth?
        if ( isset($vec_options['depth']) )
        {
            $category_query->depth($vec_options['depth']);
        }

        // Filter by category_parent?
        if ( isset($vec_options['category_parent_id']) )
        {
            $category_query->category_parent($vec_options['category_parent_id']);
        }

        return $category_query->all();
    }


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
     * Return an array with all the parents for a Category model
     */
    public function getAllParents(Category $category_model) : array
    {
        $vec_output = [];
        if ( $category_model->categoryParent )
        {
            $vec_output[] = $category_model->categoryParent;

            return ArrayHelper::merge($vec_output, $this->getAllParents($category_model->categoryParent));
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
    public function getCategoryList(string $category_type, array $vec_options = []) : array
    {
        $vec_output = [];

        $vec_category_models = $this->getCategoriesByType($category_type, $vec_options);

        if ( !empty($vec_category_models) )
        {
            foreach ( $vec_category_models as $category_model )
            {
                $vec_output[$category_model->category_id] = $category_model->fullTitle();

                // Include all children
                if ( isset($vec_options['is_include_children']) )
                {
                    $vec_category_children_models = $category_model->getAllChildren();
                    if ( !empty($vec_category_children_models) )
                    {
                        foreach ( $vec_category_children_models as $category_children_model )
                        {
                            $vec_output[$category_children_model->category_id] = $category_children_model->fullTitle();
                        }
                    }
                }
            }
        }

        return $vec_output;
    }


    /**
     * Return a Category list grouped by parent
     */
    public function getCategoryGroupedList(string $category_type, array $vec_options = []) : array
    {
        $vec_output = [];

        // Get an array with all the parent categories [ <category_id> => <category_name> ]
        $vec_parent_category_list = Yii::$app->categoryManager->getCategoryList($category_type, ['depth' => 0]);

        foreach ( $vec_parent_category_list as $parent_category_id => $parent_category_name )
        {
            $vec_children_category_list = Yii::$app->categoryManager->getCategoryList($category_type, ['category_parent_id' => $parent_category_id, 'is_include_children' => true]);

            if ( ! isset($vec_options['is_select_parent']) && ! isset($vec_options['is_filter_parent']) )
            {
                $vec_output[StringHelper::strtoupper($parent_category_name)] = $vec_children_category_list;
            }

            // Parent is selectable
            else
            {
                $parent_category_label = $parent_category_name;

                // Add "-> Seleccionar" prefix
                if ( isset($vec_options['is_select_parent']) )
                {
                    $parent_category_label = " ". Yii::t('backend', '-> Seleccionar'). " {$parent_category_name}";
                }

                // Add "(TODOS)" suffix
                else if ( isset($vec_options['is_filter_parent']) )
                {
                    $parent_category_label = "{$parent_category_name} (". Yii::t('backend', 'TODOS'). ")";
                }

                $vec_output[StringHelper::strtoupper($parent_category_name)] = ArrayHelper::merge(
                    [
                        $parent_category_id => $parent_category_label,
                    ],
                    $vec_children_category_list
                );
            }
        }

        return $vec_output;
    }
}
