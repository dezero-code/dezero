<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use dezero\behaviors\TimestampBehavior;
use dezero\behaviors\UuidBehavior;
use dezero\contracts\TitleInterface;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\entity\models\Entity;
use dezero\modules\entity\models\StatusHistory;
use Dz;
use Yii;
use yii\db\ActiveQueryInterface;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of Entity objects.
 */
abstract class ActiveRecord extends \dezero\db\ActiveRecord implements TitleInterface
{
    use DisableTrait;
    use EnableTrait;
    use StatusTrait;


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                TimestampBehavior::class,
                UuidBehavior::class
            ]
        );
    }


    /**
     * @return ActiveQueryInterface The relational query object.
     */
    public function getEntity() : ActiveQueryInterface
    {
        return $this->hasOne(Entity::class, ['entity_uuid' => 'entity_uuid']);
    }


    /**
     * Return entity type
     */
    public function getEntityType() : string
    {
        return $this->tableName();
    }


    /**
     * {@inheritdoc}
     */
    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes);

        // Makes sure that the Entity model exists
        $this->ensureEntityExists();
    }


    /**
     * {@inheritdoc}
     */
    public function title() : string
    {
        return $this->getSourceName();
    }


    /**
     * Generate and return source_name attribute value
     */
    public function getSourceName() : mixed
    {
        $vec_keys = static::primaryKey();
        if ( count($vec_keys) > 1 )
        {
            $vec_values = [];
            foreach ( $vec_keys as $attribute_name )
            {
                $vec_values = $this->getAttribute($attribute_name);
            }
            return implode("-", $vec_values);
        }

        return $this->getAttribute($vec_keys[0]);
    }


    /**
     * Makes sure that the Entity model exists
     */
    private function ensureEntityExists() : void
    {
        if ( ! $this->entity )
        {
            // First of all, get primary key data
            $source_name = $this->getSourceName();
            $source_id = is_int($source_name) ? $source_name : null;

            // Now, create the Entity model
            $entity_model = Dz::makeObject(Entity::class);
            $entity_model->setAttributes([
                'entity_type'   => $this->getEntityType(),
                'entity_uuid'   => $this->entity_uuid,
                'source_id'     => (int)$source_id,
                'source_name'   => (string)$source_name,
                'module_name'   => $this->getModuleName()
            ]);

            if ( ! $entity_model->save() )
            {
                Log::saveModelError($entity_model);
            }
        }
    }


    /**
     * Return module name from current model
     */
    private function getModuleName() : string
    {
        $class_name = self::className();
        $class_name = str_replace('dezero\\modules\\', '', $class_name);
        $class_name = str_replace('\\models\\'. StringHelper::basename(get_class($this)), '', $class_name);
        return $class_name;
    }
}
