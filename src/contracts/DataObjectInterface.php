<?php
/**
 * DataObject contract
 */

namespace dezero\contracts;

interface DataObjectInterface
{
    /**
     * Create the new data object
     */
    public static function from($data) : self;


    /**
     * Return the data value
     */
    public function value();


    /**
     * Return the original data value
     */
    public function original();


    /**
     * Check if data object is empty
     */
    public function empty() : bool;
}
