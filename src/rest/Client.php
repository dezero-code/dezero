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
class Client extends Component implements ConfigInterface
{
    /**
     * API name
     */
    protected $client_name;


    /**
     * @var \dezero\rest\ClientConfigurator
     */
    protected $configurator;


    /**
     * @var yii\httpclient\Client;
     */
    private $http_client;


    /**
     * Constructor
     */
    public function __construct(string $client_name = 'default')
    {
        $this->client_name = $client_name;
        $this->init();
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
     * Return HTTP Client
     */
    public function getClient() : HttpClient
    {
        if ( !is_object($this->http_client) )
        {
            $this->http_client = Yii::createObject([
                'class' => HttpClient::class,
                'baseUrl' => $this->config->getBaseUrl(),
            ]);
        }

        return $this->http_client;
    }


    /**
     * Send a GET request to an URI
     */
    public function get(string $uri, array $vec_query_string = [], array $vec_headers = [] )
    {
        return $this->send($uri, 'GET', $vec_query_string, $vec_headers);
    }


    /**
     * Send an HTTP request
     */
    public function send(string $uri, string $method = 'GET', array $vec_input = [], array $vec_headers = [] )
    {
        switch ( $method )
        {
            case 'GET':
                // return $this->client->get($uri, $vec_input)->send();
            break;

            case 'POST':
                // return $this->client->post($uri, $vec_input)->send();
            break;
        }

    }

}
