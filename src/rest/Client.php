<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\contracts\ConfigInterface;
use dezero\entity\ActiveRecord;
use dezero\helpers\Log;
use dezero\modules\api\models\ApiLog;
use dezero\rest\ClientConfigurator;
use Dz;
use yii\base\Component;
use yii\helpers\Json;
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
     * @var \yii\web\Request
     */
    protected $request;


    /**
     * @var \yii\web\Response
     */
    protected $response;


    /**
     * @var \dezero\modules\api\models\ApiLog
     */
    private $api_log_model;


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



    public function setRequest($request) : void
    {
        $this->request = $request;
    }


    public function setResponse($response) : void
    {
        $this->response = $response;
    }


    /*
    |--------------------------------------------------------------------------
    | LOGS
    |--------------------------------------------------------------------------
    */

    /**
     * Save request and response into a LOG (database or file)
     */
    public function saveLog(?string $log_category = null) : bool
    {
        if ( $log_category === null )
        {
            $log_category = $this->config->getLogCategory();
        }

        // Process input parameters
        $input_data = $this->request->getData();
        if ( empty($input_data) )
        {
            $input_data = $this->request->getContent();
        }
        $input_data = is_array($input_data) ? Json::encode($input_data) : $input_data;

        switch ( $this->config->getLogDestination() )
        {
            // Save log in "http_client.log" file (or "<log_category>.log" file)
            case 'file':
                // Request
                $log_message  = "\n";
                $log_message .= " - Endpoint: {$this->request->getFullUrl()}\n";
                $log_message .= " - Method: {$this->request->getMethod()}\n";
                $log_message .= " - URI: /{$this->request->getUrl()}\n";
                $log_message .= " - Parameters: {$input_data}\n";
                // $log_message .= " - Options: ". Json::encode($this->request->getOptions()) ."\n";

                // Response
                $response_label = $this->response->isOk ? "OK" : "ERROR";
                $log_message .= " - Response ({$response_label} - HTTP code {$this->response->getStatusCode()}): ". $this->response->getContent() ."\n";

                Yii::info($log_message, $log_category);

                return true;
            break;

            // Save log into database (ApiLog model)
            case 'db':
                $this->api_log_model = Dz::makeObject(ApiLog::class);
                $this->api_log_model->setAttributes([
                    'api_type'              => ApiLog::API_TYPE_CLIENT,
                    'api_name'              => $this->client_name,
                    'request_type'          => $this->request->getMethod(),
                    'request_url'           => $this->request->getFullUrl(),
                    'request_endpoint'      => $this->request->getMethod() .'___'. $this->request->getUrl(),
                    // 'request_hostname'      => $this->request->getUserIP(),
                    'request_input_json'    => $input_data,
                    'response_http_code'    => $this->response->getStatusCode(),
                    'response_json'         => $this->response->getContent()
                ]);
                if ( $this->api_log_model->save() )
                {
                    return true;
                }

                // Some error saving log into database
                Log::saveModelError($this->api_log_model);
            break;
        }

        return false;
    }


    /**
     * Save errors into the log
     */
    public function saveLogError() : bool
    {
        return $this->saveLog($this->config->getLogErrorCategory());
    }


    /**
     * Save debug logs
     */
    public function saveLogDebug() : bool
    {
        return $this->saveLog($this->config->getLogDebugCategory());
    }


    /**
     * Link an Entity model with last ApiLog model
     */
    public function linkEntity(ActiveRecord $entity_model) : bool
    {
        if ( $this->api_log_model === null )
        {
            return false;
        }

        return $this->api_log_model->linkEntity($entity_model);
    }
}
