<?php

/**
 * Migration class m230322_162033_language_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;
use dezero\helpers\StringHelper;

class m230322_162033_language_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "language" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('language', true);

        $this->createTable('language', [
            'language_id' => $this->string(6)->notNull(),
            'name' => $this->string(64)->notNull(),
            'native' => $this->string(64),
            'prefix' => $this->string(16)->notNull(),
            'is_ltr_direction' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(1),
            'is_default' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'weight' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(1),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Primary key
        $this->addPrimaryKey(null, 'language', 'language_id');

        // Create indexes
        $this->createIndex(null, 'language', ['entity_uuid'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'language', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'language', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'language', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        // Insert default languages: English and Spanish
        $this->insertMultiple('language', [
            [
                'language_id'       => 'es-ES',
                'name'              => 'Spanish',
                'native'            => 'Español',
                'prefix'            => 'es',
                'is_ltr_direction'  => 1,
                'is_default'        => 1,
                'weight'            => 1,
                'created_date'      => time(),
                'created_user_id'   => 1,
                'updated_date'      => time(),
                'updated_user_id'   => 1,
                'entity_uuid'       => StringHelper::UUID()
            ],
            [
                'language_id'       => 'en-US',
                'name'              => 'English (United States)',
                'native'            => 'English',
                'prefix'            => 'en',
                'is_ltr_direction'  => 1,
                'is_default'        => 0,
                'weight'            => 2,
                'created_date'      => time(),
                'created_user_id'   => 1,
                'updated_date'      => time(),
                'updated_user_id'   => 1,
                'entity_uuid'       => StringHelper::UUID()
            ],
        ]);

        // Finally, add FOREIGN KEY for "language_id" column in "user_user" table
        $this->addForeignKey(null, 'user_user', ['language_id'], 'language', ['language_id'], 'RESTRICT', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230322_162033_language_table cannot be reverted.\n";

        return false;
    }
}
