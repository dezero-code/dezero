<?php

/**
 * Migration class m231009_153235_batch_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m231009_153235_batch_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "batch" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('batch', true);

        $this->createTable('batch', [
            'batch_id' => $this->primaryKey(),
            'batch_type' => $this->string(32)->notNull(),
            'name' => $this->string(128)->notNull(),
            'description' => $this->string(255),

            // Results (items)
            'total_items' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_errors' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'total_warnings' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'summary_json' => $this->string(512),
            'results_json' => $this->longtext(),

            // Operations
            'total_operations' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'last_operation' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'item_starting_num' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'item_ending_num' => $this->integer()->unsigned()->notNull()->defaultValue(0),

            // File (optional)
            'file_id' => $this->integer()->unsigned(),

            // Entity information
            'entity_uuid' => $this->char(36),
            'entity_type' => $this->string(128),
            'entity_source_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull()
        ]);

        // Create indexes
        $this->createIndex(null, 'batch', ['batch_type'], false);
        $this->createIndex(null, 'batch', ['batch_type', 'name'], false);
        $this->createIndex(null, 'batch', ['entity_type', 'entity_source_id'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'batch', ['file_id'], 'asset_file', ['file_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'batch', ['entity_uuid'], 'entity_entity', ['entity_uuid'], 'SET NULL', null);
        $this->addForeignKey(null, 'batch', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'batch', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231009_153235_batch_table cannot be reverted.\n";

        return false;
    }
}
