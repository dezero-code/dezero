<?php

/**
 * Migration class m230322_160210_user_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;
use dezero\helpers\Str;

class m230322_160210_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "user_user" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('user_user', true);

        $this->createTable('user_user', [
            'user_id' => $this->primaryKey(),
            'username' => $this->string(255)->notNull(),
            'email' => $this->string(255)->notNull(),
            'password' => $this->string(60)->notNull(),
            'first_name' => $this->string(255),
            'last_name' => $this->string(255),
            'status_type' => $this->enum('status_type', ['active', 'disabled', 'banned', 'pending', 'deleted'])->notNull()->defaultValue('active'),
            'language_id' => $this->string(6)->notNull()->defaultValue('es-ES'),
            'last_login_date' => $this->date(),
            'last_login_ip' => $this->string(),
            'is_verified_email' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'last_verification_date' => $this->date(),
            'is_force_change_password' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'last_change_password_date' => $this->date(),
            'default_role' => $this->string(64),
            'default_theme' => $this->string(16),
            'is_superadmin' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'timezone' => $this->string(40)->notNull()->defaultValue('Europe/Madrid'),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Create UNQUE indexes for "username" and "email" columns
        $this->createIndex(null, 'user_user', ['username'], true);
        $this->createIndex(null, 'user_user', ['email'], true);

        // Create indexes
        $this->createIndex(null, 'user_user', ['status_type'], false);
        $this->createIndex(null, 'user_user', ['default_role'], false);
        $this->createIndex(null, 'user_user', ['is_superadmin'], false);
        $this->createIndex(null, 'user_user', ['entity_uuid'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'user_user', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'user_user', ['created_user_id'], 'user_user', ['user_id'], 'RESTRICT', null);
        $this->addForeignKey(null, 'user_user', ['updated_user_id'], 'user_user', ['user_id'], 'RESTRICT', null);

        // Insert superadmin user
        $this->insert('user_user', [
            'user_id'           => 1,
            'username'          => 'superadmin',
            'email'             => getenv('SITE_EMAIL'),
            'password'          => '21232f297a57a5a743894a0e4a801fc3',
            'first_name'        => 'Admin',
            'last_name'         => 'Super',
            'status_type'       => 'active',
            'is_verified_email' => 1,
            'default_role'      => 'admin',
            'default_theme'     => 'backend',
            'is_superadmin'     => 1,
            'created_date'      => time(),
            'created_user_id'   => 1,
            'updated_date'      => time(),
            'updated_user_id'   => 1,
            'entity_uuid'       => Str::UUID()
        ]);

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230322_160210_user_table cannot be reverted.\n";

        return false;
    }
}
