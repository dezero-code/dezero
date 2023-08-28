<?php

/**
 * Migration class m230828_093210_category_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230828_093210_category_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "category_category" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('category_category', true);

        $this->createTable('category_category', [
            'category_id' => $this->primaryKey(),
            'category_type' => $this->string(128)->notNull()->defaultValue('category'),
            'category_parent_id' => $this->integer()->unsigned(),
            'name' => $this->string(255)->notNull(),
            'description' => $this->text(),
            'weight' => $this->integer()->unsigned()->notNull()->defaultValue(1),
            'depth' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(0),
            'image_file_id' => $this->integer()->unsigned(),
            'language_id' => $this->string(6)->notNull()->defaultValue('es-ES'),
            'category_translated_id' => $this->integer()->unsigned(),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Create indexes
        $this->createIndex(null, 'category_category', ['category_type', 'language_id'], false);
        $this->createIndex(null, 'category_category', ['entity_uuid'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'category_category', ['category_parent_id'], 'category_category', ['category_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'category_category', ['category_translated_id'], 'category_category', ['category_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'category_category', ['image_file_id'], 'asset_file', ['file_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'category_category', ['language_id'], 'language', ['language_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'category_category', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'category_category', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'category_category', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230828_093210_category_table cannot be reverted.\n";

        return false;
    }
}
