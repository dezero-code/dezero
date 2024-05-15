<?php
/**
 * CountryQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models\query;

/**
 * ActiveQuery class for \dezero\modules\settings\models\Country.
 *
 * @see \dezero\modules\settings\models\Country
 */
class CountryQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "country_code" attribute value
     */
    public function country_code(int $country_code) : self
    {
        return $this->andWhere(['country_code' => $country_code]);
    }
}
