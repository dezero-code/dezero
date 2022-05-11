<?php
/**
 * Dz is a helper class serving common Dz and Yii framework functionality.
 */

class Dz extends Yii
{
    /**
     * Check the environment is currently running in
     */
    public static function checkEnvironment(string $environment_name) : bool
    {
        return self::env('ENVIRONMENT') === $environment_name;
    }


    /**
     * Check if app is currently running in PRODUCTION environment
     */
    public static function isProduction() : bool
    {
        return self::checkEnvironment('prod');
    }


    /**
     * Check if app is currently running in PRODUCTION environment
     *
     * Alias of Dz::is_production()
     */
    public static function isLive() : bool
    {
        return self::checkEnvironment('prod');
    }


    /**
     * Check if app is currently running in DEVELOPMENT environment
     */
    public static function isDev() : bool
    {
        return self::checkEnvironment('dev');
    }


    /**
     * Check if app is currently running in TEST environment
     */
    public static function isTest() : bool
    {
        return self::checkEnvironment('test');
    }
}
