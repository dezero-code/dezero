<?php
/**
 * EntitySearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models\search;

use dezero\contracts\SearchInterface;
use dezero\data\ActiveDataProvider;
use dezero\modules\entity\models\Entity;

/**
 * Search class for \dezero\modules\entity\models\Entity.
 *
 * @see \dezero\modules\entity\models\Entity
 */
class EntitySearch extends Entity implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['source_id', 'source_name'], 'default', 'value' => null],
            'integerFields' => [['source_id'], 'integer'],
            'safeFields' => [['entity_uuid', 'entity_type', 'source_name', 'module_name'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = Entity::find();

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


        // Compare conditions
        $query->andFilterWhere([
            'entity_uuid' => $this->entity_uuid,
            'source_id' => $this->source_id,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'entity_type', $this->entity_type])
            ->andFilterWhere(['like', 'source_name', $this->source_name])
            ->andFilterWhere(['like', 'module_name', $this->module_name]);

        return $data_provider;
    }
}
