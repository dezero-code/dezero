<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use Dz;
use yii\base\BootstrapInterface;
use yii\base\Event;

/**
 * Bootstrap class of the framework.
 */
class Bootstrap implements BootstrapInterface
{
    /**
     * {@inheritdoc}
     */
    public function bootstrap($app)
    {
        $this->registerEvents($app);
    }


    /**
     * Register event listeners
     */
    private function registerEvents($app)
    {
        // Process all the installed modules
        $vec_modules = Dz::getModules();
        if ( !empty($vec_modules) )
        {
            foreach ( $vec_modules as $module_id => $module_class )
            {
                $module = $app->getModule($module_id);

                // Check if EVENTS has been defined in the modules
                if ( $module && isset($module->events) && is_array($module->events) && !empty($module->events) )
                {
                    foreach ( $module->events as $event )
                    {
                        $event_class = $event['class'] ?? $event[0];
                        $event_name = $event['event'] ?? $event[1];
                        $event_listener = $event['callback'] ?? $event[2];
                        if ( method_exists($event_listener[0], $event_listener[1]) )
                        {
                            Event::on($event_class, $event_name, $event_listener);
                        }
                    }
                }
            }
        }
    }
}
