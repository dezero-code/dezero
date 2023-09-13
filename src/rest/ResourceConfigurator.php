<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\base\Configurator;
use dezero\contracts\ConfiguratorInterface;
use Yii;

/**
 * Controller is the base class dor RESTful API controller classess
 */
class ResourceConfigurator extends Configurator implements ConfiguratorInterface
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
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/api', $this->type);
        if ( $vec_config === null )
        {
            return [];
        }

        $this->vec_config = $vec_config;
        return $this->vec_config;

        return [];
    }


    /**
     * Return the default configuration for the specific type
     */
    public function defaultConfiguration() : array
    {
        // Try with default configuration defined on "/app/config/api"
        $vec_config = Yii::$app->config->get('components/api', 'default');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Allowed values: file (log file), db (database)
            'log_destination'   => 'file', // 'db',

            // Authorization?
            'auth' => false,

            // Allowed hosts
            'allowed_hosts'  => []
        ];
    }

    /**
     * Return configuration option/s for current type
     */
    public function getConfig(?string $config_key = null, ?string $config_subkey = null)
    {
        return ConfigHelper::getValue($this->vec_config, $config_key, $config_subkey);
    }


    /**
     * Check if auth configuration has been enabled
     */
    public function isAuth()
    {
        return $this->get('auth') !== false;
    }
}
