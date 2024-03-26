<?php
/**
 * AssetFileSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\models\search;

use dezero\contracts\SearchInterface;
use dezero\data\ActiveDataProvider;
use dezero\modules\asset\models\AssetFile;

/**
 * Search class for \dezero\modules\asset\models\AssetFile.
 *
 * @see \dezero\modules\asset\models\AssetFile
 */
class AssetFileSearch extends AssetFile implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['file_options', 'title', 'description', 'original_file_name', 'reference_entity_uuid', 'reference_entity_type'], 'default', 'value' => null],
            'integerFields' => [['file_id', 'file_size', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['file_name', 'file_path', 'file_mime', 'file_options', 'asset_type', 'title', 'description', 'original_file_name', 'reference_entity_uuid', 'reference_entity_type', 'entity_uuid'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = AssetFile::find();

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
            'file_id' => $this->file_id,
            'file_size' => $this->file_size,
            'asset_type' => $this->asset_type,
            'reference_entity_uuid' => $this->reference_entity_uuid,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            'entity_uuid' => $this->entity_uuid,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'file_name', $this->file_name])
            ->andFilterWhere(['like', 'file_path', $this->file_path])
            ->andFilterWhere(['like', 'file_mime', $this->file_mime])
            ->andFilterWhere(['like', 'file_options', $this->file_options])
            ->andFilterWhere(['like', 'title', $this->title])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'original_file_name', $this->original_file_name])
            ->andFilterWhere(['like', 'reference_entity_type', $this->reference_entity_type]);

        return $data_provider;
    }
}
