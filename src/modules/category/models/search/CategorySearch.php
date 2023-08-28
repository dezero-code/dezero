<?php
/**
 * CategorySearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\category\models\Category;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\category\models\Category.
 *
 * @see \dezero\modules\category\models\Category
 */
class CategorySearch extends Category implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['category_parent_id', 'description', 'image_file_id', 'category_translated_id', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
            'integerFields' => [['category_id', 'category_parent_id', 'weight', 'depth', 'image_file_id', 'category_translated_id', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['category_type', 'name', 'description', 'language_id', 'entity_uuid'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = Category::find();

        $data_provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize'  => 30
            ]
        ]);

        // Uncomment the following line if you do not want to return any records when validation fails
        if ( ! ( $this->load($params) && $this->validate() ) )
        {
            return $data_provider;
        }


        // Date filter
        if ( $this->created_date !== null )
        {
            $date = strtotime($this->created_date);
            $query->andFilterWhere(['between', 'created_date', $date, $date + 3600 * 24]);
        }

        /*
        // Search filter by firstname and/or lastname
        if ( $this->name_filter !== null )
        {
            $query->andFilterWhere(['OR',
                ['like', 'first_name', $this->name_filter],
                ['like', 'last_name', $this->name_filter],
                ['like', 'CONCAT(first_name, " " , last_name)', $this->name_filter]
            ]);
        }
        */

        // Compare conditions
        $query->andFilterWhere([
            'category_id' => $this->category_id,
            'category_parent_id' => $this->category_parent_id,
            'weight' => $this->weight,
            'depth' => $this->depth,
            'image_file_id' => $this->image_file_id,
            'language_id' => $this->language_id,
            'category_translated_id' => $this->category_translated_id,
            'disabled_user_id' => $this->disabled_user_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'entity_uuid' => $this->entity_uuid,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'category_type', $this->category_type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $data_provider;
    }
}
