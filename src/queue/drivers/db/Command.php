<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\queue\drivers\db;

use dezero\helpers\DateHelper;
use dezero\helpers\Json;
use dezero\helpers\QueueHelper;
use dezero\helpers\StringHelper;
use dezero\queue\drivers\db\Queue;
use yii\console\Exception;
use yii\console\ExitCode;
use yii\console\widgets\Table;
use yii\helpers\Console;
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
     * @inheritdoc
     */
    public function actions()
    {
        return [];
    }


     /**
     * Re-add a failed message(s) into the queue.
     *
     * > ./yii queue/retry-all
     */
    public function actionRetryAll()
    {
        $this->stdout(PHP_EOL);

        // Total messages by status
        $vec_messages = QueueHelper::getByStatus(Queue::STATUS_TYPE_FAILED);

        if ( empty($vec_messages) )
        {
            $this->stdout("No FAILED messages in the queue.", Console::FG_RED);
            $this->stdout(PHP_EOL);

            return ExitCode::OK;
        }

        $total_messages = count($vec_messages);
        $this->stdout("Re-adding {$total_messages} failed " . ($total_messages === 1 ? 'message' : 'messages') . ' back into the queue ... ');

        QueueHelper::retryAll();

        $this->stdout('Done!' . PHP_EOL, Console::FG_GREEN);

        return ExitCode::OK;
    }


    /**
     * Displays all ACTIVE messages (included FAILED)
     *
     * > ./yii queue/info
     */
    public function actionInfo()
    {
        // Intro
        $this->stdout(PHP_EOL);
        $this->stdout("╔═════════════════════════╗", Console::FG_BLUE);
        $this->stdout(PHP_EOL);
        $this->stdout("║    QUEUE INFORMATION    ║", Console::FG_BLUE);
        $this->stdout(PHP_EOL);
        $this->stdout("╚═════════════════════════╝", Console::FG_BLUE);
        $this->stdout(PHP_EOL . PHP_EOL);


        // --------------------------------------------
        // TOTALS
        // --------------------------------------------

        // Total messages by status
        $vec_totals = QueueHelper::totalByStatus();

        if ( empty($vec_totals) )
        {
            $this->stdout("Queue is empty", Console::FG_RED);
            $this->stdout(PHP_EOL);

            return ExitCode::OK;
        }

        foreach ( $vec_totals as $vec_total_status )
        {
            $this->stdout("  - ");
            $this->formatStatus($vec_total_status['status_type']);
            $this->stdout(": {$vec_total_status['total_messages']}", Console::BOLD);
            $this->stdout(PHP_EOL);
        }
        $this->stdout(PHP_EOL);


        // --------------------------------------------
        // ACTIVE MESSAGES TABLE
        // --------------------------------------------

        $vec_messages = QueueHelper::getByStatus([Queue::STATUS_TYPE_WAITING, Queue::STATUS_TYPE_RESERVED, Queue::STATUS_TYPE_FAILED]);
        if  ( ! $vec_messages )
        {
            $this->stdout("No active messages in queue", Console::FG_RED);
            $this->stdout(PHP_EOL);

            return ExitCode::OK;
        }

        $vec_rows = [];
        foreach ( $vec_messages as $message )
        {
            $message['message'] = print_r(Json::decode($message['message']) ,true);
            $message['status_type'] = StringHelper::strtoupper($message['status_type']);
            $vec_rows[] = [$message['message_id'], $message['message'], $message['status_type'], $message['attempt'], DateHelper::toFormat($message['updated_date'])];
        }

        $vec_rows = (new Table())
            ->setHeaders(['ID', 'Message', 'Status', 'Attempts', 'Last updated at'])
            ->setRows($vec_rows)
            ->run();
        $this->stdout($vec_rows);

        return ExitCode::OK;
    }


    /**
     * Print status with color and style
     */
    private function formatStatus($status_type)
    {
        $status_label = StringHelper::strtoupper($status_type);
        switch ( $status_type )
        {
            case Queue::STATUS_TYPE_WAITING:
                $this->stdout($status_label, Console::FG_YELLOW, Console::BOLD);
            break;

            case Queue::STATUS_TYPE_RESERVED:
                $this->stdout($status_label, Console::FG_BLUE, Console::BOLD);
            break;

            case Queue::STATUS_TYPE_COMPLETED:
                $this->stdout($status_label, Console::FG_GREEN, Console::BOLD);
            break;

            case Queue::STATUS_TYPE_FAILED:
                $this->stdout($status_label, Console::FG_RED, Console::BOLD);
            break;

            default:
                $this->stdout($status_label, Console::BOLD);
            break;
        }
    }
}
