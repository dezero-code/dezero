<?php
/**
 * Migration class m240517_070137_currency_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;
use dezero\helpers\StringHelper;

class m240517_070137_currency_table extends Migration
{
     /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "currency" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('currency', true);

        $this->createTable('currency', [
            'currency_code' => $this->string(3)->notNull(),  // ISO 4217. Example: 'EUR'
            'name' => $this->string(64)->notNull(),         // Currency name in English
            'symbol' => $this->string(8)->notNull(),        // Currency symbol. Example: '€'
            'numeric_code' => $this->smallInteger(4)->unsigned()->notNull(),
            'format' => $this->string(16)->notNull(),
            'minor_unit' => $this->string(16),
            'major_unit' => $this->string(16),
            'thousands_separator' => $this->string(16),
            'decimal_separator' => $this->string(16),
            'decimals_number' => $this->tinyInteger(2)->unsigned()->notNull()->defaultValue(2),
            'weight' => $this->smallInteger(4)->unsigned()->notNull()->defaultValue(1),
            'is_default' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'currency', 'currency_code');

        // Create indexes
        $this->createIndex(null, 'currency', ['numeric_code'], true);
        $this->createIndex(null, 'currency', ['is_default'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'currency', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'currency', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'currency', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        // Insert EUR currency
        $this->insertEuroCurrency();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240517_070137_currency_table cannot be reverted.\n";

        return false;
    }


    /**
     * Insert EUR currency
     */
    private function insertEuroCurrency()
    {
        $this->insert('currency', [
            'currency_code'         => 'EUR',
            'name'                  => 'Euro',
            'symbol'                => '€',
            'numeric_code'          => 978,
            'format'                => '{price} &euro;',
            'minor_unit'            => 'Cent',
            'major_unit'            => 'Euro',
            'thousands_separator'   => '.',
            'decimal_separator'     => ',',
            'decimals_number'       => 2,
            'weight'                => 1,
            'is_default'            => 1,
            'created_date'          => time(),
            'created_user_id'       => 1,
            'updated_date'          => time(),
            'updated_user_id'       => 1,
            'entity_uuid'           => StringHelper::UUID()
        ]);
    }
}
