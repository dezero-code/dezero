<?php
/**
 * BatchQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\sync\models\query;

/**
 * ActiveQuery class for \dezero\modules\sync\models\Batch.
 *
 * @see \dezero\modules\sync\models\Batch
 */
class BatchQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "batch_id" attribute value
     */
    public function batch_id(int $batch_id) : self
    {
        return $this->andWhere(['batch_id' => $batch_id]);
    }


    /**
     * Filter the query by "batch_type" attribute value
     */
    public function batch_type(string $batch_type) : self
    {
        return $this->andWhere(['batch_type' => $batch_type]);
    }


    /**
     * Filter the query by "name" attribute value
     */
    public function name(string $name) : self
    {
        return $this->andWhere(['name' => $name]);
    }
}
