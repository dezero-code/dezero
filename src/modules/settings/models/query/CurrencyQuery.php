<?php
/**
 * CurrencyQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\models\query;

/**
 * ActiveQuery class for \dezero\modules\settings\models\Currency.
 *
 * @see \dezero\modules\settings\models\Currency
 */
class CurrencyQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "currency_code" attribute value
     */
    public function currency_code(int $currency_code) : self
    {
        return $this->andWhere(['currency_code' => $currency_code]);
    }

}
