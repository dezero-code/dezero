<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\contracts\ConfigInterface;
use dezero\helpers\StringHelper;
use dezero\rest\ResourceConfigurator;
use dezero\traits\ErrorTrait;
use Dz;
use yii\helpers\Json;
use Yii;

/**
 * Controller is the base class dor RESTful API controller classess
 */
abstract class Resource extends \yii\base\BaseObject implements ConfigInterface
{
    use ErrorTrait;


    /**
     * API name
     */
    protected $api_name;


    /**
     * @var \dezero\base\Configurator
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
     * Constructor
     */
    public function __construct(string $api_name = 'default')
    {
        $this->api_name = $api_name;

        $this->init();
    }


    /**
     * Initializes the object
     */
    public function init() : void
    {
        // Get HTTP request method
        $this->request_method = Yii::$app->request->getMethod();

        // Load a specific configuration
        $this->getConfig();

        // Load input parameters
        $this->loadInput();

        // Init response
        $this->vec_response = [
            'status_code'   => 403,
            'errors'        => ['Access denied']
        ];
    }


    /*
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


    /**
     * Return the request method
     */
    public function getMethod() : string
    {
        return $this->request_method;
    }


    /*
     * Return the received input
     */
    public function getInput() : array
    {
        return $this->vec_input;
    }


    /**
     * Load input parameters
     */
    public function loadInput() : void
    {
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
     * Validate process
     */
    public function validate() : bool
    {
        // Check auth
        if ( $this->checkAuth() )
        {
            return true;
        }

        $this->sendError(401, ['Unauthorized']);

        return false;
    }


    /**
     * Run the resource
     */
    abstract public function run() : void;


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


    /**
     * Gets RestFul data and decodes its JSON request
     */
    public function jsonInput()
    {
        return Json::decode(file_get_contents('php://input'));
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


    /**
     * Return response
     */
    public function sendResponse(bool $is_save_log = true) : array
    {
        // Check if we have errors
        if ( $this->hasErrors() )
        {
            return $this->sendErrors();
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

        $this->vec_response = [
            'status_code'   => $status_code,
            'errors'        => $vec_errors
        ];

        return $this->vec_response;
    }


    /**
     * Return all the errors
     */
    public function sendErrors() : array
    {
        return $this->sendError();
    }
}
