<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\rbac;

class Role extends Item
{
    /**
     * {@inheritdoc}
     */
    public $type = self::TYPE_ROLE;


    /**
     * {@inheritdoc}
     */
    public $item_type = self::ITEM_TYPE_ROLE;
}
