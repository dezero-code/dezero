<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\contracts\ConfigInterface;
use dezero\rest\ClientConfigurator;
use Dz;
use yii\base\Component;
use yii\httpclient\Client as HttpClient;
use Yii;

/**
 * HTTP Client class for REST API communication
 */
class Client extends HttpClient implements ConfigInterface
{
    /**
     * Client name
     */
    protected $client_name;


    /**
     * @var \dezero\rest\ClientConfigurator
     */
    protected $configurator;


    /**
     * Constructor
     */
    public function __construct(string $client_name = 'bc', array $vec_config = [])
    {
        $this->init();
        parent::__construct($vec_config);
    }


    /**
     * Initializes the object
     */
    public function init() : void
    {
        // Load a specific configuration
        $this->getConfig();
    }


    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : ClientConfigurator
    {
        if ( $this->configurator === null )
        {
            $this->configurator = Dz::makeObject(ClientConfigurator::class, [$this->client_name]);
        }

        return $this->configurator;
    }


    /**
     * Return API client base URL
     */
    public function getBaseUrl() : string
    {
        return $this->config->getBaseUrl();
    }


    /**
     * Return API client auth URL
     */
    public function getAuthUrl() : string
    {
        return $this->config->getAuthUrl();
    }


    /**
     * Check if debug mode is enabled
     */
    public function isDebug() : bool
    {
        return $this->config->isDebug();
    }
}
