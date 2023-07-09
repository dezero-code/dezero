<?php
/**
 * StatusHistorySearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\entity\models\StatusHistory;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\entity\models\StatusHistory.
 *
 * @see \dezero\modules\entity\models\StatusHistory
 */
class StatusHistorySearch extends StatusHistory
 implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['entity_source_id', 'comments'], 'default', 'value' => null],
            'integerFields' => [['status_history_id', 'entity_source_id', 'created_date', 'created_user_id'], 'integer'],
            'safeFields' => [['entity_type', 'entity_uuid', 'status_type', 'comments'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = StatusHistory::find();

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
            'status_history_id' => $this->status_history_id,
            'entity_uuid' => $this->entity_uuid,
            'entity_source_id' => $this->entity_source_id,
            'created_user_id' => $this->created_user_id,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'entity_type', $this->entity_type])
            ->andFilterWhere(['like', 'status_type', $this->status_type])
            ->andFilterWhere(['like', 'comments', $this->comments]);

        return $data_provider;
    }
}
