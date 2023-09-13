<?php
/**
 * Configurator contract
 */

namespace dezero\contracts;

interface ConfiguratorInterface
{
    /**
     * Return configuration value(s) for current type
     */
    public function getConfig(string $config_key = null, ?string $config_subkey = null);


    /**
     * Load a configuration for the specific type
     */
    public function loadConfiguration() : array;


    /**
     * Return the default configuration for the specific type
     */
    public function defaultConfiguration() : array;
}
