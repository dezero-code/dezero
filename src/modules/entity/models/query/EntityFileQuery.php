<?php
/**
 * EntityFileQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models\query;

/**
 * ActiveQuery class for \dezero\modules\entity\models\EntityFile.
 *
 * @see \dezero\modules\entity\models\EntityFile
 */
class EntityFileQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "entity_file_id" attribute value
     */
    public function entity_file_id(int $entity_file_id) : self
    {
        return $this->andWhere(['entity_file_id' => $entity_file_id]);
    }


    /**
     * Filter the query by "entity_uuid" attribute value
     */
    public function entity_uuid(string $entity_uuid) : self
    {
        return $this->andWhere(['entity_uuid' => $entity_uuid]);
    }
}
