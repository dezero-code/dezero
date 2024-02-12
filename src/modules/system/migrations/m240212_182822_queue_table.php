<?php
/**
 * Migration class m240212_182822_queue_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m240212_182822_queue_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "queue" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('queue', true);

        $this->createTable('queue', [
            'job_id' => $this->primaryKey(),
            'channel' => $this->string()->notNull()->defaultValue('queue'),
            'job' => $this->binary()->notNull(),
            'ttr' => $this->integer()->unsigned()->notNull(),
            'delay' => $this->integer()->unsigned()->notNull()->defaultValue(0),
            'priority' => $this->integer()->unsigned()->notNull()->defaultValue(1024),

            // Status information
            'status_type' => $this->enum('status_type', ['waiting', 'executing', 'completed', 'failed'])->notNull()->defaultValue('waiting'),
            'executing_date' => $this->date(),
            'completed_date' => $this->date(),
            'failed_date' => $this->date(),
            'is_failed' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),

            // More information
            'progress' => $this->smallInteger()->notNull()->defaultValue(0),
            'progress_label' => $this->string(),
            'attempt' => $this->integer()->unsigned(),
            'results_json' => $this->text(),

            // Entity information
            'entity_uuid' => $this->char(36),
            'entity_type' => $this->string(128),
            'entity_source_id' => $this->integer()->unsigned(),

            'created_date' => $this->date()->notNull(),
            'updated_date' => $this->date()->notNull()
        ]);

        // Create indexes
        $this->createIndex(null, 'queue', ['channel', 'status_type'], false);
        $this->createIndex(null, 'queue', ['channel', 'is_failed'], false);
        $this->createIndex(null, 'queue', ['entity_type', 'entity_source_id'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'queue', ['entity_uuid'], 'entity_entity', ['entity_uuid'], 'SET NULL', null);

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_182822_queue_table cannot be reverted.\n";

        return false;
    }
}
