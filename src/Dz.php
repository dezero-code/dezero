<?php
/**
 * Dz is a helper class serving common Dz and Yii framework functionality.
 */

class Dz extends Yii
{
    /**
     * @inheritdoc
     */
    public static function createObject($type, array $params = [])
    {
        return parent::createObject($type, $params);
    }


    /**
     * Get current controller name
     */
    public static function currentController(bool $is_lowercase = false) : ?string
    {
        return self::$app->controller->id;
    }


    /**
     * Get current action name
     */
    public static function currentAction(bool $is_lowercase = false) : ?string
    {
        return self::$app->controller->action->id;
    }


    /**
     * Get current module name
     */
    public static function currentModule(bool $is_lowercase = false) : ?string
    {
        return self::$app->controller->module->id;
    }


    /**
     * Get current theme name
     */
    public static function currentTheme(bool $is_lowercase = false) : ?string
    {
        return self::$app->view->theme ? self::$app->view->theme->name : null;
    }


    /**
     * Returns an environment variable, checking for it in `$_SERVER` and calling `getenv()` as a fallback.
     */
    public static function env($name) : ?string
    {
        return isset($_SERVER[$name]) ? getenv($name) : null;
    }


    /**
     * Returns an environment variable, checking for it in `$_SERVER` and calling `getenv()` as a fallback.
     */
    public static function getEnvironment() : string
    {
        return static::env('ENVIRONMENT');
    }


    /**
     * Check the environment is currently running in
     */
    public static function checkEnvironment(string $environment_name) : bool
    {
        return static::env('ENVIRONMENT') === $environment_name;
    }


    /**
     * Check if app is currently running in PRODUCTION environment
     */
    public static function isProduction() : bool
    {
        return static::checkEnvironment('prod');
    }


    /**
     * Check if app is currently running in PRODUCTION environment
     *
     * Alias of Dz::is_production()
     */
    public static function isLive() : bool
    {
        return static::checkEnvironment('prod');
    }


    /**
     * Check if app is currently running in DEVELOPMENT environment
     */
    public static function isDev() : bool
    {
        return static::checkEnvironment('dev');
    }


    /**
     * Check if app is currently running in TEST environment
     */
    public static function isTest() : bool
    {
        return static::checkEnvironment('test');
    }
}
