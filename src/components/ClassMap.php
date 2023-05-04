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
    // private $definitions;


    /**
     * ClassMap constructor.
     *
     * @param array $map
     *
    public function __construct(array $definitions = [])
    {
        $this->definitions = $definitions;
    }
    */


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
        return $this->getDi()->get($class, $params, $config);
    }


    /**
     * Returns correct namespace for a class name
     *
    public function get(string $class) : string
    {
        if ( array_key_exists($class, $this->definitions) )
        {
            return $this->definitions[$class];
        }

        return $class;
    }
    */

    /**
     * Registers a class definition with this container.
     *
    public function set(string $class, string $definition) : self
    {
        $this->definitions[$class] = $definition;

        return $this;
    }
    */
}
