<?php
/**
 * Module for user administation
 */

namespace dezero\modules\user;

use yii\base\BootstrapInterface;
use yii\base\Event;

class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        if ( $app->hasModule('user') && $app->getModule('user') instanceof Module )
        {
            $module = $app->getModule('user');

            // Register Event Handlers
            foreach ( $module->events as $event )
            {
                $event_class = $event['class'] ?? $event[0];
                $event_name = $event['event'] ?? $event[1];
                $event_listener = $event['callback'] ?? $event[2];
                if (method_exists($event_listener[0], $event_listener[1])) {
                    Event::on($event_class, $event_name, $event_listener);
                }
            }

            /*
            Event::on(
                \dezero\modules\user\events\FormEvent::class,
                \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
                function (\dezero\modules\user\events\FormEvent $event)
                {
                    // dd("onFailedLogin raised!");
                    \dezero\helpers\Log::dev("onFailedLogin raised from bootstrap (global source)!");
                }
            );


            Event::on(
                \dezero\modules\user\controllers\LoginController::class,
                \dezero\modules\user\events\FormEvent::EVENT_FAILED_LOGIN,
                function (\dezero\modules\user\events\FormEvent $event)
                {
                    dd($event);
                    // dd("onFailedLogin raised!");
                    \dezero\helpers\Log::dev("onFailedLogin raised from bootstrap (controller source)!");
                }
            );
            */
        }
    }
}
