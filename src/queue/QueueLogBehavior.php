<?php
/**
 * LogBehavior class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */
namespace dezero\queue;

use dezero\helpers\Log;
use yii\queue\ExecEvent;
use yii\queue\JobEvent;
use yii\queue\JobInterface;
use yii\queue\LogBehavior;
use Yii;
// use yii\base\Behavior;

/**
 * Log Behavior class for queue system
 */
class QueueLogBehavior extends LogBehavior
{
    /**
     * @param ExecEvent $event
     */
    public function afterError(ExecEvent $event)
    {
        $title = $this->getExecTitle($event);

        Log::queue("{$title} is finished with error(s): {$event->error}.");
    }


    /**
     * @param JobEvent $event
     * @return string
     * @since 2.0.2
     */
    protected function getJobTitle(JobEvent $event) : string
    {
        $name = $event->job instanceof JobInterface ? get_class($event->job) : 'unknown job';

        return "[$event->id] $name";
    }

    /**
     * @param ExecEvent $event
     * @return string
     * @since 2.0.2
     */
    protected function getExecTitle(ExecEvent $event) : string
    {
        $title = $this->getJobTitle($event);
        $extra = "attempt: $event->attempt";
        if ( $pid = $event->sender->getWorkerPid() )
        {
            $extra .= ", PID: $pid";
        }

        return "$title ($extra)";
    }
}
