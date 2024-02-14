<?php
/**
 * Queue helper class file for Dezero Framework
 */

namespace dezero\helpers;

use dezero\db\Query;
use Dz;
use yii\base\BaseObject;
use Yii;

/**
 * Helper class to work with queues using DATABASE DRIVER
 */
class QueueHelper extends BaseObject
{
    /**
     * Return all the messages filtered by status
     */
    public static function getByStatus(string $status_type) : ?array
    {
        return (new Query())
            ->from(self::getTableName())
            ->where(['status_type' => $status_type])
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
