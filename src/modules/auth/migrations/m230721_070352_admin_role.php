<?php

/**
 * Migration class m230721_070352_admin_role
 *
 * @link http://www.dezero.es/
 */

use dezero\helpers\AuthHelper;
use dezero\db\Migration;

class m230721_070352_admin_role extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create new role "admin"
        AuthHelper::createRole('admin', 'Administrator');

        // Assing role to user_id = 1 (superadmin)
        AuthHelper::assignRole('admin', 1);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230721_070352_admin_role cannot be reverted.\n";

        return false;
    }
}
