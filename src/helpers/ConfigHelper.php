<?php
/**
 * Config class file helper
 *
 * Helper class for working with several config files
 */

namespace dezero\helpers;

use Yii;

/**
 * Helper for creating application configurations.
 */
class ConfigHelper
{
    /**
     * Merges the given configurations into a single configuration array.
     *
     * @param array $array the configurations to merge.
     * @return array the merged configuration.
     */
    public static function merge(array $array) : array
    {
        $result = [];
        foreach ( $array as $config )
        {
            if ( is_string($config) )
            {
                if ( ! file_exists($config) )
                {
                    continue;
                }
                $config = require($config);
            }
            if ( ! is_array($config) )
            {
                continue;
            }
            $result = self::mergeArray($result, $config);
        }
        return $result;
    }


    /**
     * Merges two or more arrays into one recursively.
     *
     * @param array $a array to be merged to
     * @param array $b array to be merged from.
     * @return array the merged array.
     */
    public static function mergeArray(array $a, array $b) : array
    {
        $args = func_get_args();
        $res = array_shift($args);
        while ( ! empty($args) )
        {
            $next = array_shift($args);
            foreach ( $next as $k => $v )
            {
                if ( is_integer($k) )
                {
                    isset($res[$k]) ? $res[] = $v : $res[$k] = $v;
                }
                else
                {
                    if ( is_array($v) && isset($res[$k]) && is_array($res[$k]) )
                    {
                        $res[$k] = self::mergeArray($res[$k], $v);
                    }
                    else
                    {
                        $res[$k] = $v;
                    }
                }
            }
        }
        return $res;
    }


    /**
     * Return a configuration value given a key or subkey
     */
    public static function getValue(array $vec_config, ?string $config_key = null, ?string $config_subkey = null)
    {
        // Check if configuration key exists
        if ( !empty($vec_config) && array_key_exists($config_key, $vec_config) )
        {
            // Check if configuration option exists
            if ( $config_subkey !== null && array_key_exists($config_subkey, $vec_config[$config_key]) )
            {
                return $vec_config[$config_key][$config_subkey];
            }

            return $vec_config[$config_key];
        }

        if ( $config_key !== null )
        {
            return null;
        }

        return $vec_config;
    }
}
