<?php
/**
 * Config contract for models
 */

namespace dezero\contracts;

use dezero\entity\ConfigBuilder;

interface ConfigInterface
{
    /**
     * Return the configBuilder class to manage configuration options
     */
    public function getConfig() : ConfigBuilder;
}
