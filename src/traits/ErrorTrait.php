<?php
/**
 * Error manager trait class
 *
 * @see dezero\traits\FlashMessageTrait
 */

namespace dezero\traits;

use dezero\helpers\ArrayHelper;

trait ErrorTrait
{
    /**
     * Registered errors
     */
    public $vec_errors = [];


    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->vec_errors;
    }


    /**
     * Add error(s)
     */
    public function addError($vec_errors) : void
    {
        if ( is_array($vec_errors) )
        {
            $this->vec_errors = ArrayHelper::merge($this->vec_errors, $vec_errors);
        }
        else
        {
            $this->vec_errors[] = $vec_errors;
        }
    }
}
