<?php
/**
 * Migration class m240212_114747_batch_summary_alter_column
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class m240212_114747_batch_summary_alter_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Change "summary_json" column from VARCHAR(512) to TEXT()
        $this->alterColumn('batch', 'summary_json', $this->text());

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240212_114747_batch_summary_alter_column cannot be reverted.\n";

        return false;
    }
}
