<?php
/**
 * DataObject class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\data;

use dezero\contracts\DataObjectInterface;
use Yii;

/**
 * Base class to manage configuration options
 */
abstract class DataObject extends \yii\base\BaseObject implements DataObjectInterface
{
    /**
     * @var mixed
     */
    protected $data;


    /**
     * @var mixed
     */
    protected $original_data;


    /**
     * Main constructor
     */
    public function __construct($data)
    {
        $this->data = $data;
        $this->original_data = $data;
    }


    /**
     * Create the new data object
     */
    public static function from($data) : self
    {
        return new static($data);
    }


    /**
     * Return data property
     */
    public function value()
    {
        return $this->data;
    }


    /**
     * Return the original data given as input
     */
    public function original()
    {
        return $this->original_data;
    }


    /**
     * Check if data object is empty
     */
    public function empty() : bool
    {
        return empty($this->data);
    }
}
