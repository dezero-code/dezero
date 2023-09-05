<?php
/**
 * UuidBehavior class file
 *
 * Automatically generate and save UUID values for column "entity_uuid"
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://dezero.es/
 * @copyright Copyright &copy; 2023 Dezero
 */
namespace dezero\behaviors;

use dezero\helpers\StringHelper;
use Yii;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;

class UuidBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute that will receive the UUID value user ID value
     */
    public $uuid_attribute = 'entity_uuid';


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        if ( empty($this->attributes) )
        {
            $this->attributes = [
                BaseActiveRecord::EVENT_BEFORE_VALIDATE => $this->uuid_attribute,
            ];
        }
    }


    /**
     * {@inheritdoc}
     */
    protected function getValue($event)
    {
        if ( $this->owner->{$this->uuid_attribute} === null || $this->owner->{$this->uuid_attribute} === '0' )
        {
            return $this->generateUUID();
        }

        return $this->owner->{$this->uuid_attribute};
        // return parent::getValue($event);
    }


    /**
     * Generate an UUID value
     */
    private function generateUUID()
    {
        return StringHelper::UUID();
    }
}
