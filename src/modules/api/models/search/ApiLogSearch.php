<?php
/**
 * ApiLogSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\api\models\ApiLog;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\api\models\ApiLog.
 *
 * @see \dezero\modules\api\models\ApiLog
 */
class ApiLogSearch extends ApiLog implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['request_input_json', 'request_hostname', 'response_json', 'entity_uuid', 'entity_type', 'entity_source_id'], 'default', 'value' => null],
            'integerFields' => [['api_log_id', 'response_http_code', 'entity_source_id', 'created_date', 'created_user_id'], 'integer'],
            'safeFields' => [['api_type', 'api_name', 'request_type', 'request_url', 'request_endpoint', 'request_input_json', 'request_hostname', 'response_json', 'entity_uuid', 'entity_type'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = ApiLog::find();

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
            'api_log_id' => $this->api_log_id,
            'api_type' => $this->api_type,
            'request_type' => $this->request_type,
            'response_http_code' => $this->response_http_code,
            // 'entity_uuid' => $this->entity_uuid,
            'entity_source_id' => $this->entity_source_id,
            'created_user_id' => $this->created_user_id,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'api_name', $this->api_name])
            ->andFilterWhere(['like', 'request_url', $this->request_url])
            ->andFilterWhere(['like', 'request_endpoint', $this->request_endpoint])
            ->andFilterWhere(['like', 'request_input_json', $this->request_input_json])
            ->andFilterWhere(['like', 'request_hostname', $this->request_hostname])
            ->andFilterWhere(['like', 'response_json', $this->response_json])
            ->andFilterWhere(['like', 'entity_type', $this->entity_type]);

        return $data_provider;
    }
}
