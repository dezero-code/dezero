<?php
/**
 * Class Log
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\helpers;

/**
 * Helper for managing log messages
 */
class Log extends \DzLog
{
    /**
     * Magic method __callStatic to invoke methods like "dev", "error" or "warning"
     */
    public static function __callStatic(string $method, $args)
    {
        // echo __METHOD__ . "\n";
        $args[] = $method;
        return call_user_func_array(__CLASS__ . '::log', $args);
    }
}
