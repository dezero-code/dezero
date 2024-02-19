<?php
/*
|-----------------------------------------------------------------
| Controller class for testing queue
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\modules\queue\TestJob;
use dezero\web\Controller;
use Dz;
use Yii;

class QueueController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action
     */
    public function actionIndex()
    {
        // Testing queue jobs
        $this->testQueue();

        return $this->render('//test/test/index');
    }


    /**
     * Testing Queue jobs
     */
    public function testQueue()
    {
        $test_job = Dz::makeObject(TestJob::class,[
            'message' => 'New message added to the queue',
            'time' => date('d/m/Y - H:i')
        ]);
        $message_id = Yii::$app->queue->push($test_job);
        d($message_id);

        // Show message status
        d(Yii::$app->queue->status($message_id));

        // The job is waiting for execute.
        d(Yii::$app->queue->isWaiting($message_id));

        // Worker gets the job from queue, and executing it.
        d(Yii::$app->queue->isReserved($message_id));

        // Worker has executed the job.
        d(Yii::$app->queue->isCompleted($message_id));

        dd("----------- FINISHED QUEUE PUSH TESTS -----------");
    }
}
