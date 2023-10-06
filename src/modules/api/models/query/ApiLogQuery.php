<?php
/**
 * ApiLogQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\models\query;

/**
 * ActiveQuery class for \dezero\modules\api\models\ApiLog.
 *
 * @see \dezero\modules\api\models\ApiLog
 */
class ApiLogQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "api_log_id" attribute value
     */
    public function api_log_id(int $api_log_id) : self
    {
        return $this->andWhere(['api_log_id' => $api_log_id]);
    }


    /**
     * Filter the query by "api_name" attribute value
     */
    public function api_name(string $api_name) : self
    {
        return $this->andWhere(['api_name' => $api_name]);
    }
}
