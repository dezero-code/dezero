<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\rbac;

use yii\base\BaseObject;

/**
 * Rule represents a business constraint that may be associated with a role, permission or assignment.
 */
abstract class Rule extends BaseObject
{
    /**
     * @var string name of the rule
     */
    public $name;


    /**
     * @var int UNIX timestamp representing the rule creation time
     */
    public $created_date;


    /**
     * @var int UNIX timestamp representing the rule updating time
     */
    public $updated_date;


    /**
     * Executes the rule.
     *
     * @param string|int $user the user ID. This should be either an integer or a string representing
     * the unique identifier of a user. See [[\yii\web\User::id]].
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to [[CheckAccessInterface::checkAccess()]].
     * @return bool a value indicating whether the rule permits the auth item it is associated with.
     */
    abstract public function execute($user, $item, $params);
}
