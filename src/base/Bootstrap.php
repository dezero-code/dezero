<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\helpers\ArrayHelper;
use Dz;
use Yii;
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
        $this->registerModuleEvents($app);
    }


    /**
     * Register event listeners from modules
     *
     * 1. Search "events.php" config file in all the modules of the application
     * 2. Register the event listener
     */
    private function registerModuleEvents($app) : void
    {
        // Process all the modules
        $vec_modules = $this->getModulesList($app);
        if ( empty($vec_modules) )
        {
            return;
        }

        foreach ( $vec_modules as $module_id => $config_alias )
        {
            $config_events_path = $config_alias .'/events';
            $vec_events = Yii::$app->config->get($config_events_path);

            // Check if EVENTS has been defined in the modules
            if ( !empty($vec_events) && is_array($vec_events) )
            {
                foreach ( $vec_events as $event )
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


    /**
     * Return Yii modules and Dezero Core & Contrib modules to check migrations
     */
    private function getModulesList($app) : array
    {
        // Core modules
        $vec_modules = [];
        $vec_core_modules = Dz::getCoreModules();
        if ( !empty($vec_core_modules) )
        {
            foreach ( $vec_core_modules as $module_id => $que_module )
            {
                if ( $app->hasModule($module_id) )
                {
                    $vec_modules['core_'. $module_id] = $this->getConfigPathByModule('core_'. $module_id);
                }
            }
        }

        // Contrib LAB modules
        $vec_dzlab_modules = Dz::getContribModules();
        if ( !empty($vec_dzlab_modules) )
        {
            foreach ( $vec_dzlab_modules as $module_id => $que_module )
            {
                if ( $app->hasModule($module_id) )
                {
                    $vec_modules['dzlab_'. $module_id] = $this->getConfigPathByModule('dzlab_'. $module_id);
                }
            }
        }

        // App modules
        $vec_app_module = Dz::getModules();
        if ( !empty($vec_app_module) )
        {
            foreach ( $vec_app_module as $module_id => $que_module )
            {
                if ( $module_id !== 'gii' )
                {
                    if ( $app->hasModule($module_id) )
                    {
                        $vec_modules[$module_id] = $this->getConfigPathByModule($module_id);
                    }
                }
            }
        }

        return $vec_modules;
    }


    /**
     * Get config path given a module
     */
    private function getConfigPathByModule(string $module_id) : string
    {
        // Core modules path on "core.src.modules"
        if ( preg_match("/^core\_/", $module_id) )
        {
            $module_id = str_replace("core_", "", $module_id);
            return "@dezero/modules/{$module_id}/config";
        }

        // Dz contrib modules path on "vendor.dezero"
        else if ( preg_match("/^dzlab\_/", $module_id) )
        {
            $module_id = str_replace("dzlab_", "", $module_id);
            return "@vendor/dezero/{$module_id}/src/config";
        }

        // App modules path on "app.modules"
        return "@app/modules/{$module_id}/config";
    }
}
