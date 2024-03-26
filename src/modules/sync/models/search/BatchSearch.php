<?php
/**
 * BatchSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\sync\models\search;

use dezero\contracts\SearchInterface;
use dezero\data\ActiveDataProvider;
use dezero\modules\sync\models\Batch;

/**
 * Search class for \dezero\modules\sync\models\Batch.
 *
 * @see \dezero\modules\sync\models\Batch
 */
class BatchSearch extends Batch implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['description', 'summary_json', 'results_json', 'file_id', 'entity_uuid', 'entity_type', 'entity_source_id'], 'default', 'value' => null],
            'integerFields' => [['batch_id', 'total_items', 'total_errors', 'total_warnings', 'total_operations', 'last_operation', 'item_starting_num', 'item_ending_num', 'file_id', 'entity_source_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['batch_type', 'name', 'description', 'summary_json', 'results_json', 'entity_uuid', 'entity_type'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = Batch::find();

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
            'batch_id' => $this->batch_id,
            'total_items' => $this->total_items,
            'total_errors' => $this->total_errors,
            'total_warnings' => $this->total_warnings,
            'total_operations' => $this->total_operations,
            'last_operation' => $this->last_operation,
            'item_starting_num' => $this->item_starting_num,
            'item_ending_num' => $this->item_ending_num,
            'file_id' => $this->file_id,
            // 'entity_uuid' => $this->entity_uuid,
            'entity_source_id' => $this->entity_source_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'batch_type', $this->batch_type])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'summary_json', $this->summary_json])
            ->andFilterWhere(['like', 'results_json', $this->results_json])
            ->andFilterWhere(['like', 'entity_type', $this->entity_type]);

        return $data_provider;
    }
}
