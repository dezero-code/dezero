<?php
/**
 * Dz is a helper class serving common Dz and Yii framework functionality.
 */

use yii\base\Module;

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
     * Create a new a object instance using Yii2 Dependency Injection concept
     *
     * @see ClassMap::make()
     */
    public static function makeObject(string $class, array $params = [], array $config = []) : object
    {
        return Yii::$app->classMap->make($class, $params, $config);
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
     * Get current language
     */
    public static function currentLanguage()
    {
        return self::$app->language;
    }


    /**
     * Get default language
     *
     * @see I18N::get_default_language()
     */
    public static function defaultLanguage()
    {
        return self::currentLanguage();
        // return self::$app->i18n->get_default_language();
    }


    /**
     * App is multilanguage
     *
     * @see I18N::is_multilanguage()
     */
    public static function isMultilanguage()
    {
        return false;
        // return self::$app->i18n->is_multilanguage();
    }


    /**
     * Check if application is running on CONSOLE mode
     */
    public static function isConsole()
    {
        // return Yii::$app->request->isConsoleRequest;
        return Yii::$app instanceof \yii\console\Application;
    }


    /**
     * Check if application is running on WEB mode
     */
    public static function isWeb()
    {
        return Yii::$app instanceof \yii\web\Application;
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


    /**
     * Return all the modules list
     */
    public static function getModules() : array
    {
        $vec_modules = [];

        foreach ( self::$app->getModules() as $id => $module )
        {
            if ( $module instanceof Module )
            {
                $vec_modules[$id] = get_class($module);
            }
            elseif ( is_string($module) )
            {
                $vec_modules[$id] = $module;
            }
            elseif ( is_array($module) && isset($module['class']) )
            {
                $vec_modules[$id] = $module['class'];
            }
            else
            {
                $vec_modules[$id] = Yii::t('backend', 'Unknown type');
            }
        }

        ksort($vec_modules);

        return $vec_modules;
    }



    /**
     * Get CORE modules for Dezero Framework
     *
     * @todo Read subdirectories from @dezero/modules
     */
    public static function getCoreModules() : array
    {
        return [
            'settings'  => '\dezero\modules\settings\Module',
            'system'    => '\dezero\modules\system\Module',
            'user'      => '\dezero\modules\user\Module',
        ];
    }


    /**
     * Get CONTRIG (dzlab) modules for Dezero Framework
     *
     * @todo Read subdirectories from @vendor/dezero
     */
    public static function getContribModules() : array
    {
        $vec_contrib_modules = [];

        /*
        // Get all contrib modules from "/app/vendor/dezero" direcotyr
        $contrib_path = DZ_BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'dezero';
        $contrib_dir = Yii::$app->file->set($contrib_path);

        if ( $contrib_dir->getExists() && $contrib_dir->getIsDir() )
        {
            $vec_contrib_directories = $contrib_dir->getContents();
            foreach ( $vec_contrib_directories as $directory_path )
            {
                $vec_directory_path = explode("/", $directory_path);
                $module_id = $vec_directory_path[count($vec_directory_path) - 1];
                if ( Yii::$app->hasModule($module_id) )
                {
                    $vec_contrib_modules[$module_id] = [
                        'class' => "\dzlab\{$module_id}\Module"
                    ];
                }
            }
        }
        */

        return $vec_contrib_modules;
    }

}
