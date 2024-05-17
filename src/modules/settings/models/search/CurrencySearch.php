<?php
/**
 * CurrencySearch search class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models\search;

use dezero\contracts\SearchInterface;
use dezero\modules\settings\models\Currency;
use dezero\data\ActiveDataProvider;

/**
 * Search class for \dezero\modules\settings\models\Currency.
 *
 * @see \dezero\modules\settings\models\Currency
 */
class CurrencySearch extends Currency implements SearchInterface
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        return [
            'defaultNull' => [['minor_unit', 'major_unit', 'thousands_separator', 'decimal_separator', 'disabled_date', 'disabled_user_id'], 'default', 'value' => null],
            'integerFields' => [['numeric_code', 'decimals_number', 'weight', 'is_default', 'disabled_date', 'disabled_user_id', 'created_date', 'created_user_id', 'updated_date', 'updated_user_id'], 'integer'],
            'safeFields' => [['currency_code', 'name', 'symbol', 'format', 'minor_unit', 'major_unit', 'thousands_separator', 'decimal_separator', 'entity_uuid'], 'safe'],
            
            // Custom search filters
            // 'customFilters' => [['name_filter'], 'safe'],
        ];
    }


    /**
     * Creates data provider instance with search query applied
     */
    public function search(array $params, ?string $search_id = null) : ActiveDataProvider
    {
        $query = Currency::find();

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
            'numeric_code' => $this->numeric_code,
            'decimals_number' => $this->decimals_number,
            'weight' => $this->weight,
            'is_default' => $this->is_default,
            'disabled_user_id' => $this->disabled_user_id,
            'created_user_id' => $this->created_user_id,
            'updated_user_id' => $this->updated_user_id,
            // 'entity_uuid' => $this->entity_uuid,
        ]);

        // Like conditions
        $query->andFilterWhere(['like', 'currency_code', $this->currency_code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'symbol', $this->symbol])
            ->andFilterWhere(['like', 'format', $this->format])
            ->andFilterWhere(['like', 'minor_unit', $this->minor_unit])
            ->andFilterWhere(['like', 'major_unit', $this->major_unit])
            ->andFilterWhere(['like', 'thousands_separator', $this->thousands_separator])
            ->andFilterWhere(['like', 'decimal_separator', $this->decimal_separator]);

        return $data_provider;
    }
}
