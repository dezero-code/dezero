<?php
/**
 * EntityInterface contract for classes
 */

namespace dezero\contracts;

use dezero\entity\ActiveRecord;
use yii\db\ActiveQueryInterface;
use yii\queue\JobInterface;

interface EntityInterface
{
    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getEntity() : ActiveQueryInterface;


    /**
     * Link an Entity model with this class
     */
    public function linkEntity(ActiveRecord $entity_model, bool $is_save = true) : bool;
}
