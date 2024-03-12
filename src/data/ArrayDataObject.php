<?php
/**
 * ArrayDataObject class file
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
use dezero\helpers\Json;
use dezero\helpers\StringHelper;
use Yii;

/**
 * Data object class for arrays
 */
class ArrayDataObject extends DataObject implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var array
     */
    private $vec_data;


    /**
     * Main constructor
     */
    public function __construct($vec_data)
    {
        $this->vec_data = $vec_data;
        $this->original_data = $vec_data;
    }


    /**
     * {@inheritdoc}
     */
    public function value()
    {
        return $this->vec_data;
    }


    /**
     * {@inheritdoc}
     */
    public function original()
    {
        return $this->original_data;
    }


    /**
     * {@inheritdoc}
     */
    public function empty() : bool
    {
        return empty($this->vec_data);
    }


    /**
     * Returns the elements as a plain array.
     *
     * @return array<int|string,mixed> Plain array
     */
    public function __toArray() : array
    {
        return $this->vec_data = $this->array($this->vec_data);
    }


    /*
    |--------------------------------------------------------------------------
    | ACCESS METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns the plain array
     */
    public function all() : array
    {
        return $this->vec_data;
    }


    /**
     * Returns the value at the given position.
     */
    public function at(int $position)
    {
        $item = array_slice($this->vec_data, $position, 1);

        return !empty($item) ? current($item) : null;
    }



    /**
     * Returns the numerical index of the given key
     */
    public function index($key)
    {
        $position = array_search($key, array_keys($this->vec_data));

        return $position !== false ? $position : null;
    }


    /**
     * Retrieves the value of an array element or object property with the given key or property name.
     *
     * @see \dezero\helpers\ArrayHelper::getValue()
     */
    public function get($key, $default_value = null)
    {
        return ArrayHelper::getValue($this->vec_data, $key, $default_value);
    }


    /**
     * Checks if the given array contains the specified key
     *
     * @see \dezero\helpers\ArrayHelper::keyExists()
     */
    public function exists(string $key) : bool
    {
        return ArrayHelper::keyExists($key, $this->vec_data);
    }


    /**
     * Returns the values of a specified column in an array.
     *
     * @see \dezero\helpers\ArrayHelper::getColumn()
     */
    public function column($key_name)
    {
        return ArrayHelper::getColumn($this->vec_data, $key_name);
    }


    /**
     * Returns the first value in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstValue()
     */
    public function first()
    {
        return ArrayHelper::firstValue($this->vec_data);
    }


    /**
     * Returns the first key in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstKey()
     */
    public function firstKey()
    {
        return ArrayHelper::firstKey($this->vec_data);
    }


    /**
     * Returns the last value in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::lasttValue()
     */
    public function last()
    {
        return ArrayHelper::lastValue($this->vec_data);
    }


    /**
     * Returns the last key in a given array.
     *
     * @see \dezero\helpers\ArrayHelper::firstKey()
     */
    public function lastKey()
    {
        return ArrayHelper::lastKey($this->vec_data);
    }



    /*
    |--------------------------------------------------------------------------
    | MANIPULATE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Pushes an element onto the end of the collection
     */
    public function add($item) : void
    {
        if ( is_array($item) )
        {
            $this->vec_data = ArrayHelper::merge($this->vec_data, $item);
        }
        else
        {
            $this->vec_data[] = $item;
        }
    }


    /**
     * Alias of add method
     */
    public function push($item) : void
    {
        $this->add($item);
    }


    /**
     * Writes a value into an associative array at the key path specified
     *
     * @see \dezero\helpers\ArrayHelper::setValue()
     */
    public function set($path, $value)
    {
        return ArrayHelper::setValue($this->vec_data, $path, $value);
    }


    /**
     * Removes items with matching values from the array and returns the removed items
     *
     * @see \dezero\helpers\ArrayHelper::remove()
     */
    public function remove(string $key) : bool
    {
        return ArrayHelper::remove($this->vec_data, $key) === null;
    }



    /*
    |--------------------------------------------------------------------------
    | Countable INTERFACE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Counts the total number of elements in the map.
     */
    public function count() : int
    {
        return count($this->vec_data);
    }



    /*
    |--------------------------------------------------------------------------
    | IteratorAggregate INTERFACE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Returns an iterator for the elements.
     *
     * This method will be used by e.g. foreach() to loop over all entries:
     *  foreach( ArrayDataObject::create(['a', 'b']) as $value )
     *
     * @return \ArrayIterator<int|string,mixed> Iterator for map elements
     */
    public function getIterator() : \ArrayIterator
    {
        return new \ArrayIterator( $this->vec_data );
    }


    /**
     * Returns the elements as a plain array.
     *
     * @return array<int|string,mixed> Plain array
     */
    public function toArray() : array
    {
        return $this->vec_data = $this->array($this->vec_data);
    }


    /**
     * Returns a plain array of the given elements.
     *
     * @param mixed $elements List of elements or single value
     * @return array<int|string,mixed> Plain array
     */
    protected function array($elements) : array
    {
        if ( is_array( $elements) )
        {
            return $elements;
        }

        if ( $elements instanceof \Closure )
        {
            return (array) $elements();
        }

        if ( $elements instanceof \dezero\base\ArrayDataObject )
        {
            return $elements->toArray();
        }

        if ( is_iterable( $elements ) )
        {
            return iterator_to_array( $elements, true );
        }

        return $elements !== null ? [$elements] : [];
    }



    /*
    |--------------------------------------------------------------------------
    | ArrayAccess INTERFACE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Determines if an element exists at an offset.
     *
     * Examples:
     *  $vec_data = ArrayDataObject::create(['a' => 1, 'b' => 3, 'c' => null]);
     *  isset($vec_data['b']);
     *  isset($vec_data['c']);
     *  isset($vec_data['d']);
     *
     * Results:
     *  The first isset() will return TRUE while the second and third one will return FALSE
     *
     * @param int|string $key Key to check for
     * @return bool TRUE if key exists, FALSE if not
     */
    public function offsetExists($key) : bool
    {
        return isset($this->vec_data[$key]);
    }


    /**
     * Returns an element at a given offset.
     *
     * Examples:
     *  $vec_data = ArrayDataObject::create(['a' => 1, 'b' => 3]);
     *  $vec_data['b'];
     *
     * Results:
     *  $vec_data['b'] will return 3
     *
     * @param int|string $key Key to return the element for
     * @return mixed Value associated to the given key
     */
    public function offsetGet($key)
    {
        return $this->vec_data[$key] ?? null;
    }


    /**
     * Sets the element at a given offset.
     *
     * Examples:
     *  $vec_data = ArrayDataObject::create(['a' => 1]);
     *  $vec_data['b'] = 2;
     *  $vec_data[0] = 4;
     *
     * Results:
     *  ['a' => 1, 'b' => 2, 0 => 4]
     *
     * @param int|string|null $key Key to set the element for or NULL to append value
     * @param mixed $value New value set for the key
     */
    public function offsetSet($key, $value) : void
    {
        if ( $key !== null )
        {
            $this->vec_data[$key] = $value;
        }
        else
        {
            $this->vec_data[] = $value;
        }
    }


    /**
     * Unsets the element at a given offset.
     *
     * Examples:
     *  $vec_data = ArrayDataObject::create(['a' => 1]);
     *  unset( $vec_data['a']);
     *
     * Results:
     *  The map will be empty
     *
     * @param int|string $key Key for unsetting the item
     */
    public function offsetUnset($key) : void
    {
        unset($this->vec_data[$key]);
    }


    /*
    |--------------------------------------------------------------------------
    | UTILS METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Removes the passed characters from the left/right of all strings.
     *
     * @see \dezero\helpers\ArrayHelper::trim()
     */
    public function trim() : void
    {
        $this->vec_data = ArrayHelper::trim($this->vec_data);
    }


    /**
     * Returns the elements encoded as JSON string
     */
    public function toJson() : ?string
    {
        return Json::encode($this->vec_data);
    }

}
