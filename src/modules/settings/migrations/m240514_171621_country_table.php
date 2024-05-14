<?php
/**
 * Migration class m240514_171621_country_table
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;
use dezero\helpers\StringHelper;
use dezero\modules\settings\helpers\CountryHelper;

class m240514_171621_country_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        // Create "country" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('country', true);

        $this->createTable('country', [
            'country_code' => $this->string(2)->notNull(),  // ISO 3166-1 alpha-2. Example: 'ES'
            'alpha3_code' => $this->string(3)->notNull(),   // ISO 3166-1 alpha-3. Example: 'ESP'
            'name' => $this->string(64)->notNull(),         // Country name in English
            'name_es' => $this->string(64)->notNull(),      // Country name in Spanish
            'is_eu' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'country', 'country_code');

        // Create indexes
        $this->createIndex(null, 'country', ['alpha3_code'], true);
        $this->createIndex(null, 'country', ['is_eu'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'country', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'country', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'country', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);

        // Insert all countries
        $this->insertCountries();

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m240514_171621_country_table cannot be reverted.\n";

        return false;
    }


    /**
     * Insert all countries into "country" database table
     */
    private function insertCountries() : void
    {
        $vec_countries_en = CountryHelper::codeList('en');
        $vec_countries_es = CountryHelper::codeList('es');
        $vec_alpha3_codes = CountryHelper::mapAlpha3List();

        // Sort countries by code (ISO 3166-1 alpha-2)
        asort($vec_countries_en);

        foreach ( $vec_countries_en as $country_code => $country_name )
        {
            $this->insert('country', [
                'country_code'      => $country_code,
                'alpha3_code'       => $vec_alpha3_codes[$country_code],
                'name'              => $country_name,
                'name_es'           => $vec_countries_es[$country_code],
                'is_eu'             => $this->isEU($country_code) ? 1 : 0,
                'created_date'      => time(),
                'created_user_id'   => 1,
                'updated_date'      => time(),
                'updated_user_id'   => 1,
                'entity_uuid'       => StringHelper::UUID()
            ]);
        }
    }

    /**
     * Check if a country is part of the European Union
     */
    private function isEU(string $country_code) : bool
    {
        return in_array($country_code, ['AT', 'BE', 'BG', 'CY', 'CZ', 'DE', 'DK', 'EE', 'ES', 'FI', 'FR', 'GR', 'HR', 'HU', 'IE', 'IT', 'LT', 'LU', 'LV', 'MT', 'NL', 'PL', 'PT', 'RO', 'SE', 'SI', 'SK']);
    }
}
