<?php
/**
 * Migration class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db;

use Yii;

/**
 * Migration is the base class for representing a database migration.
 */
class Migration extends \yii\db\Migration
{
    /**
     * Creates and executes a SQL statement for dropping a DB table, if it exists.
     *
     * @param string $table The table to be dropped. The name will be properly quoted by the method.
     */
    public function dropTableIfExists(string $table, bool $disableCheckIntegrity = false) : void
    {
        // Disable "checkIntegrity" temporally
        if ( $disableCheckIntegrity )
        {
            $this->db->disableCheckIntegrity();
        }

        $time = $this->beginCommand("dropping $table if it exists");
        $this->db->createCommand()
            ->dropTableIfExists($table)
            ->execute();
        $this->endCommand($time);

        // Enable "checkIntegrity" again
        if ( $disableCheckIntegrity )
        {
            $this->db->enableCheckIntegrity();
        }
    }
}
