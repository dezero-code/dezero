<?php
/**
 * StringDataObject class file
 *
 * @see https://php-map.org/
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\data;

use dezero\data\DataObject;
use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use Yii;

/**
 * Data object class for strings
 */
class StringDataObject extends DataObject
{
    /**
     * Handles dynamic calls to custom methods for the class.
     *
     * @throws \BadMethodCallException
     */
    public function __call($name, $params)
    {
        // Allow to execute any helper methods from StringHelper class
        if ( method_exists(StringHelper::class, $name) )
        {
            // Add "data" as first param and any optional parameter given as input
            $params = !empty($params) ? ArrayHelper::merge([$this->value()], $params) : [$this->value()];

            // Call to StringHelper::$name static method
            $value = call_user_func_array([StringHelper::class, $name], $params);
            if ( is_string($value) )
            {
                $this->data = $value;

                return;
            }

            return $value;
        }

        throw new \BadMethodCallException( sprintf( 'Method %s::%s does not exist.', static::class, $name ) );
    }
}
