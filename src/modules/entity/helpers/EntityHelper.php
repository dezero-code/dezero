<?php
/**
 * Entity helper class
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\entity\helpers;

use dezero\entity\ActiveRecord as EntityActiveRecord;
use dezero\helpers\Log;
use dezero\modules\entity\models\Entity;
use Dz;

/**
 * Helper class for working with entities
 */
class EntityHelper
{
    /**
     * Create the Entity model from the EntityActiveRecord model if it doesn't exist
     */
    public static function createEntity(EntityActiveRecord $active_record) : void
    {
        // Try to get the Entity model from the relation
        $entity_model = $active_record->entity;
        if ( $entity_model )
        {
            return ;
        }

        // Try to get the Entity model directly from database
        $entity_model = Entity::find()
            ->entity_uuid($active_record->entity_uuid)
            ->one();
        if ( $entity_model )
        {
            return ;
        }

        // In this point, we need to create the Entity model in the database

        // First of all, get primary key data
        $source_name = $active_record->getSourceName();
        $source_id = $active_record->getSourceId();

        // Now, create the Entity model
        $entity_model = Dz::makeObject(Entity::class);
        $entity_model->setAttributes([
            'entity_type'   => $active_record->getEntityType(),
            'entity_uuid'   => $active_record->entity_uuid,
            'source_id'     => (int)$source_id,
            'source_name'   => (string)$source_name,
            'module_name'   => $active_record->getModuleName()
        ]);

        if ( ! $entity_model->save() )
        {
            Log::saveModelError($entity_model);
        }
    }
}
