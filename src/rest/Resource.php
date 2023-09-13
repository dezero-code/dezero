<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;

use dezero\contracts\ConfigInterface;
use dezero\helpers\ConfigHelper;
use dezero\helpers\Json;
use dezero\rest\ResourceConfigurator;
use Dz;
use Yii;

/**
 * Controller is the base class dor RESTful API controller classess
 */
abstract class Resource implements ConfigInterface
{
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
    public function __construct(?string $api_name = null)
    {
        $this->api_name = $api_name ?? 'default';

        $this->init();
    }


    /**
     * Initializes the object
     */
    public function init()
    {
        // Load a specific configuration
        $this->getConfig();

        // Get HTTP request method
        $this->request_method = Yii::$app->request->getMethod();

        // Load input parameters
        $this->loadInput();
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


    /*
     * Return the request method
     */
    public function getMethod() : string
    {
        return $this->request_method;
    }


    /**
     * Load input parameters
     */
    public function loadInput()
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
    }


    /**
     * Gets RestFul data and decodes its JSON request
     */
    public function jsonInput()
    {
        return Json::decode(file_get_contents('php://input'));
    }


    /**
     * Return response
     */
    public function sendResponse(bool $is_save_log = true)
    {
        return $this->vec_response;
    }
}
