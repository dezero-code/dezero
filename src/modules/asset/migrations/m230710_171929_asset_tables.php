<?php

/**
 * Migration class m230710_171929_asset_tables
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230710_171929_asset_tables extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /// Create "asset_file" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('asset_file', true);

        $this->createTable('asset_file', [
            'file_id' => $this->primaryKey(),
            'file_name' => $this->string(128)->notNull(),
            'file_path' => $this->string(255)->notNull(),
            'file_mime' => $this->string(128)->notNull(),
            'file_size' => $this->integer()->notNull()->defaultValue(0),
            'file_options' => $this->string(255),
            'asset_type' => $this->enum('asset_type', ['image', 'document', 'video', 'other'])->notNull()->defaultValue('document'),
            'title' => $this->string(255),
            'description' => $this->text(),
            'original_file_name' => $this->string(128),
            'reference_entity_uuid' => $this->char(36),
            'reference_entity_type' => $this->string(128),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Create indexes
        $this->createIndex(null, 'asset_file', ['reference_entity_uuid'], false);
        $this->createIndex(null, 'asset_file', ['reference_entity_uuid', 'reference_entity_type'], false);
        $this->createIndex(null, 'asset_file', ['asset_type'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'asset_file', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'asset_file', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230710_171929_asset_tables cannot be reverted.\n";

        return false;
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }


    public function down()
    {
        echo "m230710_171929_asset_tables cannot be reverted.\n";

        return false;
    }
    */
}
