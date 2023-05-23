<?php
/**
 * Contract for Validator classes
 */

namespace dezero\contracts;

interface ValidatorInterface
{
    /**
     * Validate process
     */
    public function validate() : bool;
}
