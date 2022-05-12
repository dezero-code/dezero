<?php
/**
 * Base Theme class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\base;

use Yii;
use yii\helpers\FileHelper;

/**
 * Theme represents an application theme.
 */
class Theme extends \yii\base\Theme
{
    /**
     * @var string Theme name
     */
    public $name;


    /**
    * Initializes the component.
    */
    public function init()
    {
        parent::init();

        // Add the backend theme to allow override the views from core
        $this->addBackendTheme();
    }


    /**
     * Add  the backend theme to allow override the views from core in this way:
     *   1. '@app/themes/backend'
     *   2. "@core/src/views"
     */
    private function addBackendTheme() : void
    {
        // BasePath is "@app/themes/backend"
        $backend_view_path = $this->getBasePath();

        // Add a new rule or "path map"
        $this->pathMap[$backend_view_path ] = [
            $backend_view_path,     // '@app/themes/backend>'
            "@core/src/views",      // '@core/src/views'
        ];
    }


    /**
     * Add a backend module into the "pathMap" propierty to allow the view to be overrided in this way:
     *   1. '@app/themes/backend/<module_id>'
     *   2. "@module/views"
     */
    public function addBackendModule(string $module_id) : void
    {
        // BasePath is "@app/themes/backend"
        $backend_view_path = $this->getBasePath() . '/' . $module_id;

        // Add a new rule or "path map"
        $this->pathMap[$backend_view_path] = [
            $backend_view_path,     // '@app/themes/backend/<module_id>'
            "@{$module_id}/views",  // '@module/views'
        ];
    }


    /**
     * Converts a file to a themed file if possible.
     * If there is no corresponding themed file, the original file will be returned.
     * @param string $path the file to be themed
     * @return string the themed file, or the original file if the themed version is not available.
     * @throws InvalidConfigException if [[basePath]] is not set
     */
    /*
    public function applyTo($path)
    {
        $pathMap = $this->pathMap;
        if ( empty($pathMap) )
        {
            if ( ($basePath = $this->getBasePath()) === null )
            {
                throw new InvalidConfigException('The "basePath" property must be set.');
            }
            $pathMap = [Yii::$app->getBasePath() => [$basePath]];
        }
        $path = FileHelper::normalizePath($path);
        foreach ( $pathMap as $from => $tos )
        {
            $from = FileHelper::normalizePath(Yii::getAlias($from)) . DIRECTORY_SEPARATOR;
            if ( strpos($path, $from) === 0 )
            {
                $n = strlen($from);
                foreach ( (array) $tos as $to )
                {
                    $to = FileHelper::normalizePath(Yii::getAlias($to)) . DIRECTORY_SEPARATOR;
                    $file = $to . substr($path, $n);
                    if ( preg_match("/\_header/", $file) )
                    {
                        // dd([$path, $from, $to, $file]);
                    }
                    if ( is_file($file) )
                    {
                        return $file;
                    }
                }
            }
        }

        return $path;
    }
    */
}
