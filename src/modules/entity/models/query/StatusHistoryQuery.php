<?php
/**
 * StatusHistoryQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models\query;

/**
 * ActiveQuery class for \dezero\modules\entity\models\StatusHistory.
 *
 * @see \dezero\modules\entity\models\StatusHistory
 */
class StatusHistoryQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "status_history_id" attribute value
     */
    public function status_history_id(int $status_history_id) : self
    {
        return $this->andWhere(['status_history_id' => $status_history_id]);
    }


    /**
     * Filter the query by "entity_type" attribute value
     */
    public function entity_type(string $entity_type) : self
    {
        return $this->andWhere(['entity_type' => $entity_type]);
    }
}
