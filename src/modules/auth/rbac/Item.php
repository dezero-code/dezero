<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\rbac;

use yii\base\BaseObject;

/**
 * Operation, permission or role
 */
class Item extends BaseObject
{
    const TYPE_ROLE = 1;
    const TYPE_PERMISSION = 2;
    const ITEM_TYPE_OPERATION = 'operation';
    const ITEM_TYPE_PERMISSION = 'permission';
    const ITEM_TYPE_ROLE = 'role';

    /**
     * @var int the type of the item. This should be either [[TYPE_ROLE]] or [[TYPE_PERMISSION]].
     */
    public $type;


    /**
     * @var string the internal type of the item. This should be either [[ITEM_TYPE_OPERATION]], [[ITEM_TYPE_PERMISSION]] or [[ITEM_TYPE_ROLE]].
     */
    public $item_type;


    /**
     * @var string the name of the item. This must be globally unique.
     */
    public $name;


    /**
     * @var string the item description
     */
    public $description;


    /**
     * @var string name of the rule associated with this item
     */
    public $rule_name;


    /**
     * @var mixed the additional data associated with this item
     */
    public $data;


    /**
     * @var int UNIX timestamp representing the item creation time
     */
    public $created_date;


    /**
     * @var int UNIX timestamp representing the item updating time
     */
    public $updated_date;


    /**
     * @var string UUID
     */
    public $entity_uuid;
}
