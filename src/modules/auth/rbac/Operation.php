<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\rbac;

class Operation extends Item
{
    /**
     * {@inheritdoc}
     */
    public $type = self::TYPE_PERMISSION;


    /**
     * {@inheritdoc}
     */
    public $item_type = self::ITEM_TYPE_OPERATION;
}
