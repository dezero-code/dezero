<?php
/**
 * Config class file based on crisu83.yii-consoletools.helpers package
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
}
