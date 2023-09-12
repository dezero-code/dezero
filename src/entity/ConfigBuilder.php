<?php
/**
 * ConfigBuilder class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use dezero\contracts\ConfigBuilderInterface;
use dezero\entity\ActiveRecord;
use Yii;

/**
 * Base class to handle configuration options, view files and texts
 * for a specific type/subtype of an Entity model
 */
abstract class ConfigBuilder implements ConfigBuilderInterface
{
    /**
     * Constructor
     */
    public function __construct(ActiveRecord $model, string $type, array $vec_config = [])
    {
        $this->model = $model;
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
        // Check if configuration key exists
        if ( !empty($this->vec_config) && array_key_exists($config_key, $this->vec_config) )
        {
            // Check if configuration option exists
            if ( $config_subkey !== null && array_key_exists($config_subkey, $this->vec_config[$config_key]) )
            {
                return $this->vec_config[$config_key][$config_subkey];
            }

            return $this->vec_config[$config_key];
        }

        if ( $config_key !== null )
        {
            return null;
        }

        return $this->vec_config;
    }


    /**
     * Alias of getConfig() method
     */
    public function get(string $config_key, ?string $config_subkey = null)
    {
        return $this->getConfig($config_key, $config_subkey);
    }


    /**
     * Return the view file path for current type
     */
    public function viewPath(string $view_file) : ?string
    {
        return $this->getConfig('views', $view_file);
    }


    /**
     * Return the corresponding text
     */
    public function text(string $text_key) : ?string
    {
        return $this->getConfig('texts', $text_key);
    }
}
