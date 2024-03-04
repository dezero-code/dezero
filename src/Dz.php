<?php
/**
 * Dz is a helper class serving common Dz and Yii framework functionality.
 */

use dezero\helpers\StringHelper;
use yii\base\Module;
use yii\web\NotFoundHttpException;

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
     * Create a new a object with "cleanAttributes" options enabled
     *
     * @see ClassMap::make()
     */
    public static function makeCleanObject(string $class, array $params = [], array $config = []) : object
    {
        $params[] = ['clearAttributes' => true];

        return self::makeObject($class, $params, $config);
    }


    /**
     * Returns the data model based on the primary key given.
     * If the data model is not found, a 404 HTTP exception will be raised.
     * @param string $id the ID of the model to be loaded. If the model has a composite primary key,
     * the ID must be a string of the primary key values separated by commas.
     * The order of the primary key values should follow that returned by the `primaryKey()` method
     * of the model.
     * @return ActiveRecordInterface the model found
     * @throws NotFoundHttpException if the model cannot be found
     */
    public static function loadModel($model_class, $id)
    {
        /* @var $modelClass ActiveRecordInterface */
        $keys = $model_class::primaryKey();
        if ( count($keys) > 1 )
        {
            $values = explode(',', $id);
            if ( count($keys) === count($values) )
            {
                $model = $model_class::findOne(array_combine($keys, $values));
            }
        }
        elseif ( $id !== null )
        {
            $model = $model_class::findOne($id);
        }

        if ( isset($model) )
        {
            return $model;
        }

        throw new NotFoundHttpException("Model not found: $id");
    }


    /**
     * Get current controller name
     */
    public static function currentController(bool $is_lowercase = false) : ?string
    {
        if ( ! self::$app->controller )
        {
            return null;
        }

        return $is_lowercase ? StringHelper::strtolower(self::$app->controller->id) : self::$app->controller->id;
    }


    /**
     * Get current action name
     */
    public static function currentAction(bool $is_lowercase = false) : ?string
    {
        if ( ! self::$app->controller )
        {
            return null;
        }
        return $is_lowercase ? StringHelper::strtolower(self::$app->controller->action->id) : self::$app->controller->action->id;
    }


    /**
     * Get current module name
     */
    public static function currentModule(bool $is_lowercase = false) : ?string
    {
        if ( ! self::$app->controller || ! self::$app->controller->hasProperty('module') || ! self::$app->controller->module )
        {
            return null;
        }

        return $is_lowercase ? StringHelper::strtolower(self::$app->controller->module->id) : self::$app->controller->module->id;
    }


    /**
     * Get current theme name
     */
    public static function currentTheme(bool $is_lowercase = false) : ?string
    {
        if ( ! self::$app->view->theme )
        {
            return null;
        }

        return $is_lowercase ? StringHelper::strtolower(self::$app->view->theme->name) : self::$app->view->theme->name;
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
            'api'       => '\dezero\modules\api\Module',
            'asset'     => '\dezero\modules\asset\Module',
            'auth'      => '\dezero\modules\auth\Module',
            'category'  => '\dezero\modules\category\Module',
            'entity'    => '\dezero\modules\entity\Module',
            'frontend'  => '\dezero\modules\frontend\Module',
            'settings'  => '\dezero\modules\settings\Module',
            'sync'      => '\dezero\modules\sync\Module',
            'system'    => '\dezero\modules\system\Module',
            'test'      => '\dezero\modules\test\Module',
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


    /**
     * Return base URL (SITE_URL)
     */
    public static function baseUrl() : string
    {
        return getenv('SITE_URL');
    }


    /**
     * Make Yii::t() method compatible with Yii1 pluralize style
     *
     *  > Yii::t('backend', 'product|products', $num_products)
     */
    public static function t($category, $message, $params = [], $language = null)
    {
        if ( preg_match("/\|/", $message) && !empty($params) && !is_array($params) )
        {
            $vec_message = explode('|', $message);
            if ( count($vec_message) === 2 )
            {
                $message = '{num, plural, =1{'. $vec_message[0] .'} other{'. $vec_message[1] .'}}';
                $params = ['num' => $params];
            }
        }

        return Yii::t($category, $message, $params, $language);
    }
}
