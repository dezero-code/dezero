<?php
/**
 * CategoryQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\models\query;

/**
 * ActiveQuery class for \dezero\modules\category\models\Category.
 *
 * @see \dezero\modules\category\models\Category
 */
class CategoryQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "category_id" attribute value
     */
    public function category_id(int $category_id) : self
    {
        return $this->andWhere(['category_id' => $category_id]);
    }


    /**
     * Filter the query by "category_parent_id" attribute value
     */
    public function category_parent(int $category_parent_id) : self
    {
        return $this->andWhere(['category_parent_id' => $category_parent_id]);
    }


    /**
     * Filter the query by "category_type" attribute value
     */
    public function category_type(string $category_type) : self
    {
        return $this->andWhere(['category_type' => $category_type]);
    }


    /**
     * Filter the query by "depth" attribute value
     */
    public function depth(int $depth) : self
    {
        return $this->andWhere(['depth' => $depth]);
    }
}
