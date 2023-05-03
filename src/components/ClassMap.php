<?php
/**
 * ClassMap component class
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\components;

use yii\base\Component;
use Yii;

/**
 * ClassMap is a MAPPER for classes names
 *
 * Based on Yii2 Container that implements a [dependency injection](http://en.wikipedia.org/wiki/Dependency_injection) container
 */
class ClassMap extends Component
{
    /**
     * @var array object definitions indexed by their types
     */
    private $definitions;


    /**
     * ClassMap constructor.
     *
     * @param array $map
     */
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;

        // Load configuration
        $vec_definitions = Yii::$app->config->get('common/class_map');
        if ( !empty($vec_definitions) )
        {
            foreach ( $vec_definitions as $class => $definition )
            {
                $this->set($class, $definition);
            }
        }
    }


    /**
     * @return Container
     */
    public function getDi()
    {
        return Yii::$container;
    }


    /**
     * Gets a class from the container.
     */
    public function make(string $class, array $params = [], array $config = []) : object
    {
        $class = $this->get($class);
        return $this->getDi()->get($class, $params, $config);
    }


    /**
     * Returns correct namespace for a class name
     */
    public function get(string $class) : string
    {
        if ( array_key_exists($class, $this->definitions) )
        {
            return $this->definitions[$class];
        }

        return $class;
    }


    /**
     * Registers a class definition with this container.
     */
    public function set(string $class, string $definition) : self
    {
        $this->definitions[$class] = $definition;

        return $this;
    }


    /**
     * Create a new instance of the requested class
     */
    public function create($class, $vec_params = [])
    {
        // User ReflectionClass
        try
        {
            $reflection = new ReflectionClass($this->get($class));
        }
        catch (\ReflectionException $e)
        {
            throw new \CException('Failed to instantiate component or class "' . $class . '".', 0, $e);
        }

        // Params is a string? If so, transform into an array
        if ( !empty($vec_params) && is_string($vec_params) )
        {
            $vec_params = [$vec_params];
        }

        if ( $reflection->isInstantiable() )
        {
            return $reflection->newInstanceArgs($vec_params);
        }

        $class_name = '\\'. $this->get($class_name);
        return new $class_name;
    }
}
