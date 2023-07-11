<?php

/**
 * Migration class m230711_105638_entity_file_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230711_105638_entity_file_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "entity_file" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('entity_file', true);

        $this->createTable('entity_file', [
            'entity_file_id' => $this->primaryKey(),
            'file_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
            'entity_type' => $this->string(128)->notNull(),
            'entity_source_id' => $this->integer()->unsigned(),
            'relation_type' => $this->string(32)->notNull(),
            'weight' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
        ]);

        // Create indexes
        $this->createIndex(null, 'entity_file', ['entity_uuid', 'relation_type'], false);
        $this->createIndex(null, 'entity_file', ['entity_type', 'entity_source_id'], false);
        $this->createIndex(null, 'entity_file', ['entity_type', 'entity_source_id', 'relation_type'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'entity_file', ['file_id'], 'asset_file', ['file_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'entity_file', ['entity_uuid'], 'entity_entity', ['entity_uuid'], 'CASCADE', null);
        $this->addForeignKey(null, 'entity_file', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'entity_file', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230711_105638_entity_asset_table cannot be reverted.\n";

        return false;
    }
}
