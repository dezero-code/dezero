<?php
/**
 * ConfigTypeBuilder class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\base\ConfigInterface;
use dezero\entity\ActiveRecord;
use Yii;

/**
 * Base class to handle configuration options, view files and texts
 * for a specific type/subtype of an Entity model
 */
abstract class ConfigTypeBuilder extends \yii\base\BaseObject implements ConfigInterface
{
    /**
     * Constructor
     */
    public function __construct(ActiveRecord $model, string $type, array $vec_config = [])
    {
        $this->model = $model;
        $this->type = $type;
        $this->vec_config = $vec_config;
    }


    /**
     * Initializes the object.
     *
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     */
    public function init()
    {
        if ( empty($this->vec_config) )
        {
            $this->vec_config = $this->defaultConfiguration();
        }
    }


    /**
     * Return the default configuration for a category type
     */
    public function defaultConfiguration()
    {
        return [];
    }


    /**
     * Return configuration option/s for current type
     */
    public function getConfig(?string $config_key = null, ?string $config_option = null)
    {
        // Check if configuration key exists
        if ( !empty($this->vec_config) && $config_key !== null && array_key_exists($config_key, $this->vec_config) )
        {
            // Check if configuration option exists
            if ( $config_option !== null && array_key_exists($config_option, $this->vec_config[$config_key]) )
            {
                return $this->vec_config[$config_key][$config_option];
            }

            return $this->vec_config[$config_key];
        }

        return null;
    }


    /**
     * Return the view file path for current type
     */
    public function viewPath(string $view_file) : ?string
    {
        return $this->config('views', $view_file);
    }


    /**
     * Return the corresponding text
     */
    public function text(string $text_key) : ?string
    {
        return $this->config('texts', $text_key);
    }
}
