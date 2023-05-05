<?php

/**
 * Migration class m230505_160209_auth_tables
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230505_160209_auth_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "auth_token" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('auth_token', true);

        $this->createTable('auth_token', [
            'token_id' => $this->primaryKey(),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'token_type' =>  $this->string(32)->notNull(),
            'hash_code' => $this->string(32)->notNull(),
            'is_expired' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'is_used' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'expires_date' => $this->date(),
            'created_date' => $this->date()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Foreign Keys
        $this->addForeignKey(null, 'auth_token', ['user_id'], 'user_user', ['user_id'], 'CASCADE', null);



        // Create "auth_item" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('auth_item', true);

        $this->createTable('auth_item', [
            'name' => $this->string(64)->notNull(),
            'type' => $this->tinyInteger(1)->unsigned()->notNull(),
            'item_type' => $this->enum('item_type', ['operation', 'permission', 'role']),
            'description' => $this->string(),
            'rule_name' => $this->string(32),
            'data' => $this->string(32),
            'created_date' => $this->date()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Create indexes
        $this->addPrimaryKey(null, 'auth_item', 'name');
        $this->createIndex(null, 'auth_item', ['type'], false);
        $this->createIndex(null, 'auth_item', ['item_type'], false);



        // Create "auth_item_child" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('auth_item_child', true);

        $this->createTable('auth_item_child', [
            'parent' => $this->string(64)->notNull(),
            'child' => $this->string(64)->notNull(),
        ]);

        // Create indexes
        $this->addPrimaryKey(null, 'auth_item_child', ['parent', 'child']);
        $this->addForeignKey(null, 'auth_item_child', ['parent'], 'auth_item', ['name'], 'CASCADE', null);
        $this->addForeignKey(null, 'auth_item_child', ['child'], 'auth_item', ['name'], 'CASCADE', null);



        // Create "auth_assignment" table
        // -------------------------------------------------------------------------
        $this->createTable('auth_assignment', [
            'item_name' => $this->string(64)->notNull(),
            'item_type' => $this->enum('item_type', ['operation', 'permission', 'role']),
            'user_id' => $this->integer()->unsigned()->notNull(),
            'created_date' => $this->date()->notNull(),
        ]);
        $this->addPrimaryKey(null, 'auth_assignment', ['item_name', 'user_id']);
        $this->addForeignKey(null, 'auth_assignment', ['item_name'], 'auth_item', ['name'], 'CASCADE', null);
        $this->addForeignKey(null, 'auth_assignment', ['user_id'], 'user_user', ['user_id'], 'CASCADE', null);


        // Create "auth_rule" table
        // -------------------------------------------------------------------------
        $this->createTable('auth_rule', [
            'name' => $this->string(64)->notNull(),
            'data' => $this->string(32),
            'created_date' => $this->date()->notNull(),
            'updated_date' => $this->date()->notNull()
        ]);
        $this->addPrimaryKey(null, 'auth_rule', ['name']);


        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        return false;
    }
}
