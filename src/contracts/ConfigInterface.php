<?php
/**
 * Config contract for classes
 */

namespace dezero\contracts;

use dezero\base\Configurator;

interface ConfigInterface
{
    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : Configurator;
}
