<?php
/**
 * Migration class {ClassName}
 *
 * This view is used by dezero/console/controllers/MigrateController.php.
 *
 * The following variables are available in this view:
 */
/* @var $className string the new migration class name without namespace */
/* @var $namespace string the new migration class namespace */

echo "<?php\n";
if ( ! empty($namespace) )
{
    echo "\nnamespace {$namespace};\n";
}
?>

/**
 * Migration class <?= $className . "\n" ?>
 *
 * @link http://www.dezero.es/
 */

use dezero\db\Migration;

class <?= $className ?> extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        /*
        // Create "my_table" table
        // -------------------------------------------------------------------------
        $this->dropTableIfExists('my_table', true);

        $this->createTable('my_table', [
            'id' => $this->primaryKey(),
            'name' => $this->string(128),
            'description' => $this->text(),
            'reference_id' => $this->integer()->unsigned()->notNull(),
            'is_default' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'content_type' => $this->enum('content_type', ['page', 'block', 'text']),
            'weight' => $this->tinyInteger()->unsigned()->notNull()->defaultValue(1),
            'disabled_date' => $this->date(),
            'disabled_user_id' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_user_id' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_user_id' => $this->integer()->unsigned()->notNull(),
            'entity_uuid' => $this->uuid(),
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'my_table', 'id');

        // Create indexes
        $this->createIndex(null, 'my_table', ['entity_uuid'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'my_table', ['disabled_user_id'], 'user_user', ['user_id'], 'SET NULL', null);
        $this->addForeignKey(null, 'my_table', ['created_user_id'], 'user_user', ['user_id'], 'CASCADE', null);
        $this->addForeignKey(null, 'my_table', ['updated_user_id'], 'user_user', ['user_id'], 'CASCADE', null);


        /*
        // Add new column via $this->addColumn
        $this->addColumn('my_table', 'description', $this->string(255)->after('name'));
        */

        /*
        // Alter column via $this->alterColumn
        $this->alterColumn('my_table', 'name', $this->string(128)->notNull());
        */

        /*
        // Rename column via $this->renameColumn
        $this->renameColumn('my_table', 'name', 'new_name');
        */

        /*
        // Rename table via $this->renameTable
        $this->renameTable('my_table', 'new_name');
        */

        /*
        // Disable FOREIGH KEY check integrity
        $this->db->disableCheckIntegrity();

        $this->dropTableIfExists('my_table');

        // Enable again FOREIGH KEY check integrity
        $this->db->enableCheckIntegrity();

        // Insert default values
        // Add namespace above ---> use dezero\helpers\Str;
        $this->insert('my_table', [
            'name'              => 'name',
            'is_default'        => 1,
            'weight'            => 1,
            'created_date'      => time(),
            'created_user_id'   => 1,
            'updated_date'      => time(),
            'updated_user_id'   => 1,
            'entity_uuid'       => Str::UUID()
        ]);

        // Insert multiples values
        $this->insertMultiple('my_table', [
            [
                'name'              => 'name',
                'is_default'        => 1,
                'weight'            => 1,
                'created_date'      => time(),
                'created_user_id'   => 1,
                'updated_date'      => time(),
                'updated_user_id'   => 1,
                'entity_uuid'       => Str::UUID()
            ],
            [
                'name'              => 'name 2',
                'is_default'        => 0,
                'weight'            => 2,
                'created_date'      => time(),
                'created_user_id'   => 1,
                'updated_date'      => time(),
                'updated_user_id'   => 1,
                'entity_uuid'       => Str::UUID()
            ],
        ]);
        */
    }


    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "<?= $className ?> cannot be reverted.\n";

        return false;
    }


    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }


    public function down()
    {
        echo "<?= $className ?> cannot be reverted.\n";

        return false;
    }
    */
}
