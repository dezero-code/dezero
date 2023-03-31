<?php

/**
 * Migration class m230331_150751_user_session_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m230331_150751_user_session_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "user_session" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('user_session', true);

        // Note: According to the php.ini setting of session.hash_function,
        // you may need to adjust the length of the id column. For example,
        // if session.hash_function=sha256, you should use a length 64 instead of 40.
        $this->createTable('user_session', [
            'session_id' => $this->char(64)->notNull(),
            'user_id' => $this->integer()->unsigned(),
            'expires_date' => $this->date()->notNull(),
            'data' => $this->binary(),
            'created_date' => $this->date()->notNull(),
            'entity_uuid' => $this->uuid()
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'user_session', 'session_id');

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'user_session', ['user_id'], 'user_user', ['user_id'], 'SET NULL', null);
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m230331_150751_user_session_table cannot be reverted.\n";

        return false;
    }
}
