<?php
/*
|-----------------------------------------------------------------
| TestJob class for Testing purposes
|-----------------------------------------------------------------
*/

namespace demo\queue;

use yii\base\BaseObject;
use yii\base\Exception;
use yii\queue\JobInterface;
use Yii;

class TestJob extends BaseObject implements JobInterface
{
    public $message;
    public $time;

    /**
     * Execute the job from the queue
     */
    public function execute($queue)
    {
        sleep(1);

        // SUCESS MESSAGE
        // \DzLog::dev("Queue job is executed with message {$this->message} and time {$this->time}");

        // FAILED MESSAGE
        try {
            if ( $this->message === 'error' )
            {
                \DzLog::dev("This variable {$new} does not exist");
            }
            else
            {
                \DzLog::dev("Queue job is executed with message {$this->message} and time {$this->time}");
            }
        } catch (Exception $e) {
            return;
        }
        //  throw new Exception('This is an exception.');
    }
}
