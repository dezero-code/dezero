<?php

/**
 * Migration class m240113_094911_user_roles_permissions
 *
 * @link http://www.dezero.es/
 */

use dezero\helpers\AuthHelper;
use dezero\db\Migration;

class m240113_094911_user_roles_permissions extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create permissions for USER module
        AuthHelper::createPermission('user_manage', 'User - Full access');
        AuthHelper::createPermission('user_edit', 'User - Edit access');
        AuthHelper::createPermission('user_view', 'User - View access');

        // Assign permissions to ADMIN role
        AuthHelper::assignPermissionToRole('admin', 'user_manage');
        AuthHelper::assignPermissionToRole('admin', 'user_edit');
        AuthHelper::assignPermissionToRole('admin', 'user_view');

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240113_094911_user_roles_permissions cannot be reverted.\n";

        return false;
    }
}
