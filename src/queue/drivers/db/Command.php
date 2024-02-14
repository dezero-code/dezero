<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\queue\drivers\db;

use dezero\helpers\DateHelper;
use dezero\helpers\QueueHelper;
use dezero\queue\drivers\db\Queue;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\queue\db\Command as CoreCommand;
use Yii;

/**
 * Manages application db-queue
 */
class Command extends CoreCommand
{
    /**
     * @var Queue
     */
    public $queue;


    /**
     * Displays all FAILED messages
     *
     * > ./yii queue/failed
     */
    public function actionFailed()
    {
        $vec_messages = QueueHelper::getByStatus(Queue::STATUS_TYPE_FAILED);
        if  ( ! $vec_messages ) {
            $this->stdout("No failed jobs found");
            $this->stdout(PHP_EOL);
            return ExitCode::OK;
        }

        $vec_rows = [];
        foreach ( $vec_messages as $message )
        {
            $vec_rows[] = [$message['message_id'], $message['message'], $message['attempt'], DateHelper::toFormat($message['failed_date'])];
        }

        $vec_rows = (new Table())
            ->setHeaders(['ID', 'Message', 'Attempts', 'Failed at'])
            ->setRows($vec_rows)
            ->run();
        $this->stdout($vec_rows);

        return ExitCode::OK;
    }
}
