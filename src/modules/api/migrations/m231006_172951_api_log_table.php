<?php

/**
 * Migration class m231006_172951_api_log_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m231006_172951_api_log_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "api_log" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('api_log', true);

        $this->createTable('api_log', [
            'api_log_id' => $this->primaryKey(),
            'api_name' => $this->string(32)->notNull()->defaultValue('default'),
            'request_type' => $this->enum('request_type', ['GET', 'POST', 'PUT', 'DELETE'])->notNull()->defaultValue('GET'),
            'request_url' => $this->string(512)->notNull(),
            'request_endpoint' => $this->string(128)->notNull(),
            'request_input_json' => $this->longtext(),
            'request_hostname' => $this->string(128),
            'response_http_code' => $this->smallInteger(4)->unsigned()->notNull()->defaultValue(200),
            'response_json' => $this->longtext(),
            'entity_uuid' => $this->char(36),
            'entity_type' => $this->string(128),
            'entity_source_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull()
        ]);

        // Create indexes
        $this->createIndex(null, 'api_log', ['api_name'], false);
        $this->createIndex(null, 'api_log', ['api_name', 'request_endpoint'], false);
        $this->createIndex(null, 'api_log', ['entity_type', 'entity_source_id'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'api_log', ['entity_uuid'], 'entity_entity', ['entity_uuid'], 'CASCADE', null);
        $this->addForeignKey(null, 'api_log', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m231006_172951_api_log_table cannot be reverted.\n";

        return false;
    }
}
