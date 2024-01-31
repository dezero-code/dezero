<?php
/**
 * Base Array class file
 *
 * @see https://php-map.org/
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\StringHelper;
use Yii;

/**
 * ArrayCollection is the base class to manage arrays
 */
class ArrayCollection extends \yii\base\BaseObject
{
    /**
     * @var array
     */
    private $vec_items;


    /**
     * Array constructor
     */
    public function __construct(array $vec_items)
    {
        $this->vec_items = $vec_items;
    }


    /**
     * Returns the plain array
     */
    public function all() : array
    {
        return $this->vec_items;
    }


    /**
     * Create the new ArrayCollection object
     */
    public static function create(array $vec_items) : self
    {
        return new static($vec_items);
    }


    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     *
     * @see \dezero\helpers\ArrayHelper::getValue()
     */
    public function get($key, $default_value = null)
    {
        return ArrayHelper::getValue($this->vec_items, $key, $default_value);
    }


    /**
     * Returns the value at the given position.
     */
    public function at(int $position)
    {
        $item = array_slice($this->vec_items, $position, 1);

        return !empty($item) ? current($item) : null;
    }



    /**
     * Returns the numerical index of the given key
     */
    public function index($key)
    {
        $position = array_search($key, array_keys($this->vec_items));

        return $position !== false ? $position : null;
    }


    /**
     * Pushes an element onto the end of the collection
     */
    public function add($item) : void
    {
        if ( is_array($item) )
        {
            $this->vec_items = ArrayHelper::merge($this->vec_items, $item);
        }
        else
        {
            $this->vec_items[] = $item;
        }
    }


    /**
     * Writes a value into an associative array at the key path specified
     *
     * @see \dezero\helpers\ArrayHelper::setValue()
     */
    public function set($path, $value)
    {
        return ArrayHelper::setValue($this->vec_items, $path, $value);
    }


    /**
     * Checks if the given array contains the specified key
     *
     * @see \dezero\helpers\ArrayHelper::keyExists()
     */
    public function exists(string $key) : bool
    {
        return ArrayHelper::keyExists($key, $this->vec_items);
    }


    /**
     * Removes items with matching values from the array and returns the removed items
     *
     * @see \dezero\helpers\ArrayHelper::remove()
     */
    public function remove(string $key) : bool
    {
        return ArrayHelper::remove($this->vec_items, $key) === null;
    }


    /**
     * Returns the values of a specified column in an array.
     *
     * @see \dezero\helpers\ArrayHelper::getColumn()
     */
    public function column($key_name)
    {
        return ArrayHelper::getColumn($this->vec_items, $key_name);
    }


    /**
     * Returns the first value in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstValue()
     */
    public function first()
    {
        return ArrayHelper::firstValue($this->vec_items);
    }


    /**
     * Returns the first key in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstKey()
     */
    public function firstKey()
    {
        return ArrayHelper::firstKey($this->vec_items);
    }


    /**
     * Returns the last value in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::lasttValue()
     */
    public function last()
    {
        return ArrayHelper::lastValue($this->vec_items);
    }


    /**
     * Returns the last key in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstKey()
     */
    public function lastKey()
    {
        return ArrayHelper::lastKey($this->vec_items);
    }


    /**
     * Removes the passed characters from the left/right of all strings.
     *
     * @see \dezero\helpers\ArrayHelper::trim()
     */
    public function trim() : void
    {
        $this->vec_items = ArrayHelper::trim($this->vec_items);
    }


    /**
     * Returns the elements encoded as JSON string
     */
    public function toJson() : ?string
    {
        return Json::encode($this->vec_items);
    }
}
