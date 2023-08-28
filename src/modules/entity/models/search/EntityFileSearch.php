<?php
/**
 * EntityFileSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\entity\models\EntityFile;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\entity\models\EntityFile.
 *
 * @see \dezero\modules\entity\models\EntityFile
 */
class EntityFileSearch extends EntityFile implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['entity_source_id'], 'default', 'value' => null],
            'integerFields' => [['entity_file_id', 'file_id', 'entity_source_id', 'weight', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['entity_uuid', 'entity_type', 'relation_type'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = EntityFile::find();

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
            'entity_file_id' => $this->entity_file_id,
            'file_id' => $this->file_id,
            'entity_uuid' => $this->entity_uuid,
            'entity_source_id' => $this->entity_source_id,
            'weight' => $this->weight,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'entity_type', $this->entity_type])
            ->andFilterWhere(['like', 'relation_type', $this->relation_type]);

        return $data_provider;
    }
}
