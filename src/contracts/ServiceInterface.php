<?php
/**
 * Service contract for use cases
 */

namespace dezero\contracts;

interface ServiceInterface
{
    /**
     * Runs the use case or service
     */
    public function run() : bool;
}
