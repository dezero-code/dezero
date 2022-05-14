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
            'is_important' => $this->tinyInteger(1)->unsigned()->notNull()->defaultValue(0),
            'content_type' => $this->enum('content_type', ['page', 'block', 'text']),
            'disable_date' => $this->date(),
            'disable_uid' => $this->integer()->unsigned(),
            'created_date' => $this->date()->notNull(),
            'created_uid' => $this->integer()->unsigned()->notNull(),
            'updated_date' => $this->date()->notNull(),
            'updated_uid' => $this->integer()->unsigned()->notNull(),
            'uuid' => $this->uuid(),
        ]);

        // Primary key (alternative method)
        $this->addPrimaryKey(null, 'my_table', 'id');

        // Create indexes
        $this->createIndex(null, 'my_table', ['name'], false);

        // Create FOREIGN KEYS
        $this->addForeignKey(null, 'my_table', ['disable_uid'], 'user_users', ['id'], 'SET NULL', null);
        $this->addForeignKey(null, 'my_table', ['created_uid'], 'user_users', ['id'], 'CASCADE', null);
        $this->addForeignKey(null, 'my_table', ['updated_uid'], 'user_users', ['id'], 'CASCADE', null);


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
