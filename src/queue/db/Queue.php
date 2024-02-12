<?php
/**
 * Queue class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\queue\db;

use Yii;

/**
 * DB drive Queue
 */
class Queue extends \yii\queue\db\Queue
{
    /**
     * @var int The time (in seconds) to wait for mutex locks to be released when attempting to reserve new jobs.
     */
    public $mutexTimeout = 5;
}
