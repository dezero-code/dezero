<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rbac;

class Role extends Item
{
    /**
     * {@inheritdoc}
     */
    public $type = self::TYPE_ROLE;


    /**
     * {@inheritdoc}
     */
    public $itme_type = self::ITEM_TYPE_ROLE;
}
