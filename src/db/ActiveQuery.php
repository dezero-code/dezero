<?php
/**
 * ActiveQuery class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\db;

use Yii;

/**
 * ActiveQuery represents a DB query associated with an Active Record class.
 */
class ActiveQuery extends \yii\db\ActiveQuery
{
    /**
     * Filter the query by "entity_uuid" attribute value
     */
    public function uuid(string $uuid) : self
    {
        return $this->andWhere(['entity_uuid' => $uuid]);
    }


    /**
     * Filter the query by enabled elements
     */
    public function enabled() : self
    {
        // $this->andWhere(['is_disabled' => 0]);
        $this->andWhere('disabled_date IS NULL');

        return $this;
    }


    /**
     * Filter the query by disabled elements
     */
    public function disabled() : self
    {
        // $this->andWhere(['is_disabled' => 1]);
        $this->andWhere('disabled_date IS NOT NULL');

        return $this;
    }
}
