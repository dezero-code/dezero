<?php
/**
 * Queue helper class file for Dezero Framework
 */

namespace dezero\helpers;

use dezero\db\Query;
use dezero\queue\drivers\db\Queue;
use Dz;
use yii\base\BaseObject;
use Yii;

/**
 * Helper class to work with queues using DATABASE DRIVER
 */
class QueueHelper extends BaseObject
{
    /**
     * Check if queue is enabled
     */
    public static function isEnabled() : bool
    {
        return Dz::env('QUEUE_ENABLED') === "true";
    }


    /**
     * Return all the messages filtered by status
     */
    public static function getByStatus($status_type) : ?array
    {
        return (new Query())
            ->from(self::getTableName())
            ->where(['status_type' => $status_type])
            ->orderBy(['updated_date' => SORT_DESC])
            ->all(self::getDb());
    }


    /**
     * Return total messages grouped by status
     */
    public static function totalByStatus() : array
    {
        return (new Query())
            ->select(['COUNT(*) AS total_messages', 'status_type'])
            ->from(self::getTableName())
            ->groupBy(['status_type'])
            ->orderBy(['status_type' => SORT_ASC])
            ->all(self::getDb());
    }


    /**
     * Get message by ID
     */
    public static function getMessage(int $message_id) : ?array
    {
        return (new Query())
            ->from(self::getTableName())
            ->where(['message_id' => $message_id])
            ->one(self::getDb());
    }


    /**
     * Insert a message in database (push a message into the queue)
     */
    public static function pushMessage(array $vec_columns) : ?int
    {
        self::getDb()->createCommand()->insert(self::getTableName(), $vec_columns)->execute();

        $tableSchema = self::getDb()->getTableSchema(self::getTableName());

        return self::getDb()->getLastInsertID($tableSchema->sequenceName);
    }


    /**
     * Update a message in database
     */
    public static function updateMessage(int $message_id, array $vec_columns) : void
    {
        self::getDb()->createCommand()->update(self::getTableName(), $vec_columns, ['message_id' => $message_id])->execute();
    }


    /**
     * Delete a message in database
     */
    public static function deleteMessage(int $message_id) : bool
    {
        return (bool) self::getDb()->createCommand()->delete(self::getTableName(), ['message_id' => $message_id])->execute();
    }


    /**
     * Retries all failed messages
     */
    public static function retryAll() : void
    {
        $vec_columns = [
            'status_type'   => Queue::STATUS_TYPE_WAITING,
            'reserved_date' => null,
            'failed_date'   => null,
            'is_failed'     => 0,
            'attempt'       => null,
            'results_json'  => null
        ];
        self::getDb()->createCommand()->update(self::getTableName(), $vec_columns, ['status_type' => Queue::STATUS_TYPE_FAILED])->execute();
    }


    /**
     * DB table name
     */
    private static function getTableName() : string
    {
        return Yii::$app->queue->tableName;
    }


    /**
     * DB Connection
     */
    private static function getDb()
    {
        return Yii::$app->queue->db;
    }
}
