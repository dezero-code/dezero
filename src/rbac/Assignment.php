<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rbac;

use Yii;
use yii\base\BaseObject;

/**
 * Assignment represents an assignment of a role/permission to a user.
 */
class Assignment extends BaseObject
{
    /**
     * @var string|int user ID (see [[\yii\web\User::id]])
     */
    public $user_id;


    /**
     * @var string the item name
     */
    public $item_name;

    /**
     * @var string the internal type of the item. This should be either [[ITEM_TYPE_OPERATION]], [[ITEM_TYPE_PERMISSION]] or [[ITEM_TYPE_ROLE]].
     */
    public $item_type;


    /**
     * @var int UNIX timestamp representing the assignment creation time
     */
    public $created_date;
}
