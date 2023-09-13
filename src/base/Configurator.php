<?php
/**
 * Configurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\contracts\ConfiguratorInterface;
use dezero\helpers\ConfigHelper;
use Yii;

/**
 * Base class to manage configuration options
 */
abstract class Configurator implements ConfiguratorInterface
{
    /**
     * Constructor
     */
    public function __construct(string $type, array $vec_config = [])
    {
        $this->type = $type;
        $this->vec_config = $vec_config;

        $this->init();
    }


    /**
     * Initializes the object
     *
     * Try to load configuration via loadConfiguration() method.
     * If not, use a default configuration via defaultConfiguration() method.
     */
    public function init()
    {
        if ( empty($this->vec_config) )
        {
            // Load a specific configuration
            $vec_config = $this->loadConfiguration();

            // Load the default configuration
            if ( empty($this->vec_config) )
            {
                $this->vec_config = $this->defaultConfiguration();
            }
        }
    }


    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        return [];
    }


    /**
     * Return the default configuration for the specific type
     */
    public function defaultConfiguration() : array
    {
        return [];
    }


    /**
     * Return configuration option/s for current type
     */
    public function getConfig(?string $config_key = null, ?string $config_subkey = null)
    {
        return ConfigHelper::getValue($this->vec_config, $config_key, $config_subkey);
    }


    /**
     * Alias of getConfig() method
     */
    public function get(string $config_key, ?string $config_subkey = null)
    {
        return $this->getConfig($config_key, $config_subkey);
    }
}
