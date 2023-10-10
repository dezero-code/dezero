<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\contracts\ConfigInterface;
use dezero\entity\ActiveRecord;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\StringHelper;
use dezero\modules\api\models\ApiLog;
use dezero\rest\ResourceConfigurator;
use dezero\traits\WarningTrait;
use Dz;
use Yii;

/**
 * Controller is the base class dor RESTful API controller classess
 */
abstract class Resource extends \yii\base\BaseObject implements ConfigInterface
{
    use WarningTrait;


    /**
     * API name
     */
    protected $api_name;


    /**
     * @var \dezero\rest\ResourceConfigurator
     */
    protected $configurator;


    /**
     * HTTP request method
     */
    protected $request_method;


    /**
     * Array with input parameters
     */
    protected $vec_input;


    /**
     * Array with HTTP response
     */
    protected $vec_response;


    /**
     * Registered errors
     */
    private $vec_errors = [];


    /**
     * @var \dezero\modules\api\models\ApiLog
     */
    private $api_log_model;


    /**
     * Constructor
     */
    public function __construct(string $api_name = 'default', array $vec_config = [])
    {
        $this->api_name = $api_name;
        $this->init();
        parent::__construct($vec_config);
    }


    /**
     * Initializes the object
     */
    public function init() : void
    {
        // Load a specific configuration
        $configurator = $this->getConfig();

        // Load input parameters
        $this->loadInput();

        // Init response
        $this->vec_response = [
            'status_code'   => 403,
            'errors'        => ['Access denied']
        ];
    }


    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : ResourceConfigurator
    {
        if ( $this->configurator === null )
        {
            $this->configurator = Dz::makeObject(ResourceConfigurator::class, [$this->api_name]);
        }

        return $this->configurator;
    }


   /*
    |--------------------------------------------------------------------------
    | REQUEST / INPUT
    |--------------------------------------------------------------------------
    */

    /*
     * Return the received input
     */
    public function getInput(?string $param = null)
    {
        if ( $param === null )
        {
            return $this->vec_input;
        }

        return $this->vec_input[$param] ?? null;
    }


    /**
     * Load input parameters
     */
    private function loadInput() : void
    {
        // Get HTTP request method
        $this->request_method = Yii::$app->request->getMethod();

        switch ( $this->request_method )
        {
            case 'POST':
            case 'PUT':
            case 'DELETE':
                $this->vec_input = $this->jsonInput();
            break;

            case 'GET':
                $this->vec_input = Yii::$app->request->get();
            break;
        }

        // Ensure "vec_input" is an array
        if ( $this->vec_input === null )
        {
            $this->vec_input = [];
        }
    }


    /**
     * Return the request method
     */
    public function getMethod() : string
    {
        return $this->request_method;
    }


    /**
     * Gets RestFul data and decodes its JSON request
     */
    private function jsonInput()
    {
        return Json::decode(file_get_contents('php://input'));
    }



    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    /**
     * Validate process
     */
    public function validate() : bool
    {
        // Check auth
        if ( $this->checkAuth() )
        {
            return true;
        }

        $this->addError('Unauthorized', 401);

        return false;
    }


    /**
     * Validate required parameters
     */
    public function validateRequired(array $vec_parameters) : bool
    {
        $is_valid = true;
        foreach ( $vec_parameters as $parameter_name )
        {
            $vec_input = $this->getInput();
            if ( ! array_key_exists($parameter_name, $vec_input) )
            {
                $is_valid = false;
                $this->addError(Yii::t('backend', 'The parameter \'{name}\' is required', [
                    'name' => $parameter_name
                ]));
            }
        }

        return $is_valid;
    }


    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    /**
     * Check authorization
     */
    public function checkAuth() : bool
    {
        if ( $this->config->isAuth() )
        {
            $current_controller = Dz::currentController();
            if ( $current_controller !== 'auth' )
            {
                // Get ['auth']['token'] from api.php configuration file
                $token = $this->config->get('auth', 'token');
                if ( !empty($token) )
                {
                    return $this->checkAuthToken($token);
                }
            }
        }

        return true;
    }


    /**
     * Check authorization via simple token comparison
     */
    public function checkAuthToken($auth_token, $auth_prefix = 'Bearer')
    {
        // Get HTTP headers
        $header_auth_token = Yii::$app->request->getHeaders()->get('Authorization');

        // Check if header Authorization has been defined
        if ( $header_auth_token === null )
        {
            return false;
        }

        // Compare auth header
        $auth_header = $auth_token;
        if ( !empty($auth_prefix) )
        {
            $auth_header = $auth_prefix .' '. $auth_header;
        }

        return $header_auth_token === $auth_header;
    }



    /*
    |--------------------------------------------------------------------------
    | EXECUTION
    |--------------------------------------------------------------------------
    */

    /**
     * Run the resource
     */
    abstract public function run() : void;



    /*
    |--------------------------------------------------------------------------
    | RESPONSE / OUTPUT
    |--------------------------------------------------------------------------
    */

    /**
     * Return response
     */
    public function sendResponse(bool $is_save_log = true) : array
    {
        // Check if we have errors
        if ( $this->hasErrors() )
        {
            $status_code = $this->vec_response['status_code'] ?? 400;
            return $this->sendErrors($status_code);
        }

        // Check if we have warnings
        if ( $this->hasWarnings() )
        {
            $this->vec_response['warnings'] = $this->getWarnings();
        }

        // Save log?
        if ( $is_save_log )
        {
            $this->saveLog();
        }

        return $this->vec_response;
    }


    /**
     * Send a custom error
     */
    public function sendError(int $status_code = 400, ?array $vec_errors = null) : array
    {
        // Get errors
        if ( $vec_errors === null && $this->hasErrors() )
        {
            $vec_errors = $this->getErrors();
        }
        if ( empty($vec_errors) )
        {
            $vec_errors = ['Bad Request'];
        }

        // Avoid to send "status_code" as 1 (success)
        if ( $status_code === 1 )
        {
            $status_code = 400;
        }

        $this->vec_response = [
            'status_code'   => $status_code,
            'errors'        => $vec_errors
        ];

        // Save log error
        $this->saveLogError($status_code);

        return $this->vec_response;
    }


    /**
     * Return all the errors
     */
    public function sendErrors(int $status_code = 400) : array
    {
        return $this->sendError($status_code);
    }


    /**
     * Set error code (status code)
     */
    public function setErrorCode(int $status_code = 400) : void
    {
        $this->vec_response['status_code'] = $status_code;
    }


    /*
    |--------------------------------------------------------------------------
    | ERRORS
    |--------------------------------------------------------------------------
    */

    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->vec_errors;
    }


    /**
     * @return bool
     */
    public function hasErrors() : bool
    {
        return !empty($this->vec_errors);
    }


    /**
     * Add error(s)
     */
    public function addError($vec_errors, int $status_code = 400) : void
    {
        if ( is_array($vec_errors) )
        {
            $this->vec_errors = ArrayHelper::merge($this->vec_errors, $vec_errors);
        }
        else
        {
            $this->vec_errors[] = $vec_errors;
        }

        $this->vec_response['status_code'] = $status_code;
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

        // Input data in JSON format
        $input_data = is_array($this->vec_input) ? Json::encode($this->vec_input) : $this->vec_input;

        switch ( $this->config->getLogDestination() )
        {
            // Save log in "rest.log" file (or "<log_category>.log" file)
            case 'file':
                $log_message  = "\n";
                $log_message .= " - IP: ". Yii::$app->getRequest()->getUserIP() ."\n";
                $log_message .= " - Endpoint: ". Yii::$app->getRequest()->getPathInfo() ."\n";
                $log_message .= " - Method: ". $this->getMethod() ."\n";
                $log_message .= " - Parameters: ". $input_data ."\n";
                $log_message .= " - Reponse (HTTP code ". Yii::$app->getResponse()->getStatusCode() ."): ". Json::encode($this->vec_response) ."\n";
                $log_message .= ( $this->vec_response['status_code'] === 401 || $this->vec_response['status_code'] === 403 ) ? " - Authorization: ". Yii::$app->request->getHeaders()->get('Authorization') ."\n" : "";

                Yii::info($log_message, $log_category);

                return true;
            break;

            // Save log into database (ApiLog model)
            case 'db':
                $path_info = Yii::$app->getRequest()->getPathInfo();
                $this->api_log_model = Dz::makeObject(ApiLog::class);
                $this->api_log_model->setAttributes([
                    'api_type'              => ApiLog::API_TYPE_SERVER,
                    'api_name'              => $this->api_name,
                    'request_type'          => $this->getMethod(),
                    'request_url'           => Dz::baseUrl() . $path_info,
                    'request_endpoint'      => $this->getMethod() .'___'. $path_info,
                    'request_hostname'      => Yii::$app->getRequest()->getUserIP(),
                    'request_input_json'    => $input_data,
                    'response_http_code'    => Yii::$app->getResponse()->getStatusCode(),
                    'response_json'         => Json::encode($this->vec_response)
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
    public function saveLogError(int $status_code = 400) : bool
    {
        return $this->saveLog($this->config->getLogErrorCategory(), $status_code);
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
