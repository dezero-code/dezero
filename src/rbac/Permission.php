<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rbac;

class Permission extends Item
{
    /**
     * {@inheritdoc}
     */
    public $type = self::TYPE_PERMISSION;
}
