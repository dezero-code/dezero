<?php

/**
 * Migration class m240113_094900_category_roles_permissions
 *
 * @link http://www.dezero.es/
 */

use dezero\helpers\AuthHelper;
use dezero\db\Migration;

class m240113_094900_category_roles_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create permissions for CATEGORY module
        AuthHelper::createPermission('category_manage', 'Category - Full access');
        AuthHelper::createPermission('category_edit', 'Category - Edit access');
        AuthHelper::createPermission('category_view', 'Category - View access');

        // Assign permissions to ADMIN role
        AuthHelper::assignPermissionToRole('admin', 'category_manage');
        AuthHelper::assignPermissionToRole('admin', 'category_edit');
        AuthHelper::assignPermissionToRole('admin', 'category_view');

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240113_094900_category_roles_permissions cannot be reverted.\n";

        return false;
    }
}
