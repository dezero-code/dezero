<?php
/**
 * LanguageSearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\settings\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\settings\models\Language;
use yii\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\settings\models\Language.
 *
 * @see \dezero\modules\settings\models\Language
 */
class LanguageSearch extends Language implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['native', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
            'integerFields' => [['is_ltr_direction', 'is_default', 'weight', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['language_id', 'name', 'native', 'prefix', 'entity_uuid'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = Language::find();

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
            'language_id' => $this->language_id,
            'is_ltr_direction' => $this->is_ltr_direction,
            'is_default' => $this->is_default,
            'weight' => $this->weight,
            'disabled_user_id' => $this->disabled_user_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            // 'entity_uuid' => $this->entity_uuid,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'native', $this->native])
            ->andFilterWhere(['like', 'prefix', $this->prefix]);

        return $data_provider;
    }
}
