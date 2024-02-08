<?php
/**
 * Type contract for objects
 */

namespace dezero\contracts;

interface TypeInterface
{
    /**
     * Type used for this object
     */
    public function type() : string;
}
