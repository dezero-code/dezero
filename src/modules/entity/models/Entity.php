<?php
/**
 * Entity model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\entity\models;

use dezero\helpers\ArrayHelper;
use dezero\modules\entity\models\query\EntityQuery;
use dezero\modules\entity\models\base\Entity as BaseEntity;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * Entity model class for table "entity_entity".
 *
 * -------------------------------------------------------------------------
 * COLUMN ATTRIBUTES
 * -------------------------------------------------------------------------
 * @property string $entity_uuid
 * @property string $entity_type
 * @property int $source_id
 * @property string $source_name
 * @property string $module_name
 */
class Entity extends BaseEntity
{
    /**
     * {@inheritdoc}
     */
    public function rules() : array
    {
        /*
        return [
            // Typed rules
            'requiredFields' => [['entity_type', 'source_name', 'module_name'], 'required'],
            'integerFields' => [['source_id'], 'integer'],
            
            // Max length rules
            'max32' => [['source_name', 'module_name'], 'string', 'max' => 32],
            'max36' => [['entity_uuid'], 'string', 'max' => 36],
            'max128' => [['entity_type'], 'string', 'max' => 128],

            // Default NULL
            'defaultNull' => [['source_id', 'source_name'], 'default', 'value' => null],
            
            // UNIQUE rules
            'entityUuidUnique' => [['entity_uuid'], 'unique'],
        ];
        */

        return ArrayHelper::merge(
            parent::rules(),
            [
                // Custom validation rules
            ]
        );
    }


    /**
     * {@inheritdoc}
     *
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                // custom behaviors
            ]
        );
    }
    */


    /**
     * {@inheritdoc}
     */
    public function attributeLabels() : array
    {
        return [
            'entity_uuid' => Yii::t('entity', 'Entity Uuid'),
            'entity_type' => Yii::t('entity', 'Entity Type'),
            'source_id' => Yii::t('entity', 'Source ID'),
            'source_name' => Yii::t('entity', 'Source Name'),
            'module_name' => Yii::t('entity', 'Module Name'),
        ];
    }



    /**
     * Title used for this model
     */
    public function title() : string
    {
        return $this->entity_uuid;
    }
}
