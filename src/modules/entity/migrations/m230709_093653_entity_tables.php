<?php

/**
 * Migration class m230709_093653_entity_tables
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230709_093653_entity_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "entity_entity" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('entity_entity', true);

        $this->createTable('entity_entity', [
            'entity_uuid' => $this->uuid(),
            'entity_type' => $this->string(128)->notNull(),
            'source_id' => $this->integer()->unsigned(),
            'source_name' => $this->string(32),
            'module_name' => $this->string(32)->notNull(),
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'entity_entity', 'entity_uuid');

        // Create indexes
        $this->createIndex(null, 'entity_entity', ['entity_type'], false);
        $this->createIndex(null, 'entity_entity', ['entity_type', 'source_id'], false);
        $this->createIndex(null, 'entity_entity', ['entity_type', 'source_name'], false);



        // Create "entity_status_history" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('entity_status_history', true);

        $this->createTable('entity_status_history', [
            'status_history_id' => $this->primaryKey(),
            'entity_type' => $this->string(128)->notNull(),
            'entity_uuid' => $this->uuid(),
            'entity_source_id' => $this->integer()->unsigned(),
            'status_type' => $this->string(32)->notNull(),
            'comments' => $this->text(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
        ]);

        // Create indexes
        $this->createIndex(null, 'entity_status_history', ['entity_type'], false);
        $this->createIndex(null, 'entity_status_history', ['entity_uuid'], false);
        $this->createIndex(null, 'entity_status_history', ['entity_type', 'entity_source_id'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'entity_status_history', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230709_093653_entity_tables cannot be reverted.\n";

        return false;
    }
}
