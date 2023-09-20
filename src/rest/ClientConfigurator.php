<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\base\Configurator;
use dezero\contracts\ConfiguratorInterface;
use dezero\helpers\ConfigHelper;
use Yii;

/**
 * Controller is the base class for HTTP client class
 */
class ClientConfigurator extends Configurator implements ConfiguratorInterface
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
        $vec_config = Yii::$app->config->get('components/http_client', $this->type);
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
        // Try with default configuration defined on "/app/config/http_client"
        $vec_config = Yii::$app->config->get('components/http_client', 'default');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Debug mode?
            'debug' => true,

            // Base URL
            'base_url' => getenv('SITE_URL')
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
     * Check if debug has been enabled
     */
    public function isDebug() : bool
    {
        return $this->get('debug') === true;
    }


    /**
     * Return BASE URL
     */
    public function getBaseUrl() : string
    {
        return $this->get('base_url');
    }
}
