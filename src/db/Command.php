<?php
/**
 * Database Command class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\db;

use Yii;

/**
 * Command represents a SQL statement to be executed against a database.
 */
class Command extends \yii\db\Command
{
    /**
     * Disables integrity check
     *
     * MySQL sentence: SET FOREIGN_KEY_CHECKS=0
     */
    private function disableCheckIntegrity()
    {
        return $this->checkIntegrity(false)->execute();
    }


    /**
     * Enables integrity check
     *
     * MySQL sentence: SET FOREIGN_KEY_CHECKS=1
     */
    private function enableCheckIntegrity()
    {
        return $this->checkIntegrity(true)->execute();
    }


    /**
     * Creates and executes a SQL statement for dropping a DB table, if it exists.
     *
     * @param string $table The table to be dropped. The name will be properly quoted by the method.
     */
    public function dropTableIfExists(string $table) : Command
    {
        $sql = $this->db->getQueryBuilder()->dropTableIfExists($table);

        return $this->setSql($sql);
    }
}
