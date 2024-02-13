<?php
/**
 * Queue class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\queue\db;

use dezero\db\Query;
use dezero\helpers\Json;
use yii\base\Exception;
use yii\base\InvalidArgumentException;
use yii\di\Instance;
use yii\queue\ExecEvent;
use Yii;

/**
 * DB drive Queue
 */
class Queue extends \yii\queue\db\Queue
{
    public const STATUS_TYPE_WAITING = 'waiting';
    public const STATUS_TYPE_RESERVED = 'reserved';
    public const STATUS_TYPE_COMPLETED = 'completed';
    public const STATUS_TYPE_FAILED = 'failed';


    /**
     * @var Connection|array|string
     */
    public $db = 'db';


    /**
     * @var string table name
     */
    public $tableName = '{{%queue}}';


    /**
     * @var string
     */
    public $channel = 'queue';


    /**
     * @var int The time (in seconds) to wait for mutex locks to be released when attempting to reserve new jobs.
     */
    public $mutexTimeout = 5;


    /**
     * @var bool ability to delete completed messages from table
     */
    public $deleteCompleted = true;


    /**
     * @var int The
     */
    protected $reserveTime;


    /**
     * Listens queue and runs each message.
     *
     * @param bool $repeat whether to continue listening when queue is empty.
     * @param int $timeout number of seconds to sleep before next iteration.
     * @return null|int exit code.
     * @internal for worker command only
     * @since 2.0.2
     */
    public function run($repeat, $timeout = 0)
    {
        return $this->runWorker(function (callable $canContinue) use ($repeat, $timeout)
        {
            while ( $canContinue() )
            {
                if ( ! $this->executeJob() )
                {
                    if ( !$repeat )
                    {
                        break;
                    }
                    else if ( $timeout )
                    {
                        sleep($timeout);
                    }
                }
            }
        });
    }


    /**
     * Executes a single job.
     *
     * @param string|null $id The job ID, if a specific job should be run
     * @return bool Whether a job was found
     */
    public function executeJob(): bool
    {
        $vec_message = $this->reserve();
        if ( ! $vec_message )
        {
            return false;
        }

        if ( $this->handleMessage($vec_message['message_id'], $vec_message['message'], $vec_message['ttr'], $vec_message['attempt']) )
        {
            $this->complete($vec_message['message_id']);
        }

        return true;
    }


    /**
     * @{inheritdoc}
     */
    public function handleError(ExecEvent $event)
    {
        if ( parent::handleError($event) )
        {
            // TODO - Log error

            // Save error in database
            $this->db->createCommand()->update(
                $this->tableName,
                [
                    'status_type'   => self::STATUS_TYPE_FAILED,
                    'failed_date'   => time(),
                    'results_json'  => $event->error ? Json::encode($event->error->getMessage()) : null,
                    'updated_date'  => $vec_message['updated_date'],
                ],
                [
                    'message_id'    => $event->id,
                ])
                ->execute();
        }
    }


    /**
     * @{inheritdoc}
     */
    public function status($message_id)
    {
        $vec_message = (new Query())
            ->select(['status_type'])
            ->from($this->tableName)
            ->where(['message_id' => $message_id])
            ->one($this->db);

        if ( $vec_message )
        {
            return $vec_message['status_type'];
        }

        if ( $this->deleteCompleted )
        {
            return self::STATUS_TYPE_COMPLETED;
        }

        throw new InvalidArgumentException("Unknown message ID: $message_id.");
    }


    /**
     * @inheritdoc
     */
    protected function pushMessage($message, $ttr, $delay, $priority)
    {
        $this->db->createCommand()->insert($this->tableName, [
            'channel'       => $this->channel,
            'message'       => $message,
            'ttr'           => $ttr,
            'delay'         => $delay,
            'priority'      => $priority ?: 1024,
            'created_date'  => time(),
            'updated_date'  => time(),
        ])->execute();

        $tableSchema = $this->db->getTableSchema($this->tableName);

        return $this->db->getLastInsertID($tableSchema->sequenceName);
    }


    /**
     * {@inheritdoc}
     */
    public function remove($id)
    {
        return (bool) $this->db->createCommand()
            ->delete($this->tableName, [
                'channel' => $this->channel,
                'message_id' => $message_id
            ])
            ->execute();
    }

    /**
     * Takes one message from waiting list and reserves it for handling.
     *
     * @return array|false payload
     * @throws Exception in case it hasn't waited the lock
     */
    protected function reserve()
    {
        return $this->db->useMaster(function () {
            if ( ! $this->mutex->acquire(__CLASS__ . $this->channel, $this->mutexTimeout) )
            {
                throw new Exception('Has not waited the lock.');
            }

            try
            {
                $this->moveExpired();

                // Reserve one message
                $vec_message = (new Query())
                    ->from($this->tableName)
                    ->andWhere([
                        'channel' => $this->channel,
                        'status_type' => self::STATUS_TYPE_WAITING,
                    ])
                    ->andWhere('[[created_date]] <= :time - [[delay]]', [
                        ':time' => time()
                    ])
                    ->orderBy([
                        'priority' => SORT_ASC,
                        'message_id' => SORT_ASC
                    ])
                    ->limit(1)
                    ->one($this->db);

                if ( is_array($vec_message) )
                {
                    $vec_message['status_type'] = self::STATUS_TYPE_RESERVED;
                    $vec_message['reserved_date'] = time();
                    $vec_message['attempt'] = (int) $vec_message['attempt'] + 1;
                    $vec_message['updated_date'] = time();

                    $this->db->createCommand()->update(
                        $this->tableName,
                        [
                            'status_type'       => $vec_message['status_type'],
                            'executing_date'    => $vec_message['executing_date'],
                            'attempt'           => $vec_message['attempt'],
                            'updated_date'      => $vec_message['updated_date'],
                        ],
                        [
                            'message_id' => $vec_message['message_id'],
                        ])
                        ->execute();

                    if ( is_resource($vec_message['message']) )
                    {
                        $vec_message['message'] = stream_get_contents($vec_message['message']);
                    }
                }
            }
            finally
            {
                $this->mutex->release(__CLASS__ . $this->channel);
            }

            return $vec_message;
        });
    }


    /**
     * Mark a message as completed
     */
    protected function complete($vec_message)
    {
        // Delete completed message?
        if ( $this->deleteCompleted )
        {
            $this->db->createCommand()->delete(
                $this->tableName,
                [
                    'message_id' => $vec_message['message_id']
                ]
            )->execute();

            return;
        }

        // Keep completed message
        $this->db->createCommand()->update(
            $this->tableName,
            [
                'status_type'       => self::STATUS_TYPE_COMPLETED,
                'completed_date'    => time(),
                'updated_date'      => time()
            ],
            [
                'message_id' => $vec_message['message_id']
            ]
        )->execute();
    }


    /**
     * Mark a message as completed
     *
     * Alias of self::complete()
     */
    protected function release($vec_message)
    {
        $this->complete($vec_message);
    }


    /**
     * Moves expired messages into waiting list.
     */
    protected function moveExpired()
    {
        if ( $this->reserveTime !== time() )
        {
            $this->reserveTime = time();

            $this->db->createCommand()->update(
                $this->tableName,
                [
                    'status_type'       => self::STATUS_TYPE_WAITING,
                    'reserved_date'     => null,
                    'progress'          => 0,
                    'progress_label'    => null,
                    'updated_date'      => time()
                ],
                '[[channel]] = :channel AND [[status_type]] = :status_type AND [[reserved_date]] < :time - [[ttr]]',
                [
                    ':channel'      => $this->channel,
                    ':status_type'  => self::STATUS_TYPE_RESERVED,
                    ':time'         => $this->reserveTime
                ]
            )->execute();
        }
    }


    /**
     * @inheritdoc
     */
    public function setProgress(int $message_id, int $progress, ?string $label = null): void
    {
        $this->db->createCommand()->update(
            $this->tableName,
            [
                'progress'  => $progress,
                'progress_label' => $label,
                'updated_date'  => time(),
            ],
            [
                'message_id' => $message_id,
            ]
        )->execute();
    }
}
