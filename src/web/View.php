<?php
/**
 * View represents a view object in the MVC pattern.
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\ArrayHelper;
use Yii;
use yii\helpers\Json;

/**
 * View represents a view object in the MVC pattern.
 */
class View extends \yii\web\View
{
    /**
     * Finds the view file based on the given view name.
     *
     * @inheritdoc
     */
    protected function findViewFile($view, $context = null)
    {
        // Exclude gii module
        if ( $context !== null && preg_match("/^yii\\\gii\\\/", get_class($context) ) )
        {
            return parent::findViewFile($view, $context);
        }

        if ( strncmp($view, '@', 1) === 0 )
        {
            // e.g. "@app/views/main"
            $file = Yii::getAlias($view);
        }
        elseif ( strncmp($view, '//', 2) === 0 )
        {
            // e.g. "//layouts/main"
            $file = Yii::$app->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
        }
        elseif ( strncmp($view, '/', 1) === 0 )
        {
            // e.g. "/site/index"
            if ( Yii::$app->controller !== null )
            {
                $file = Yii::$app->controller->module->getViewPath() . DIRECTORY_SEPARATOR . ltrim($view, '/');
            }
            else
            {
                throw new InvalidCallException("Unable to locate view file for view '$view': no active controller.");
            }
        }
        elseif ( $context instanceof ViewContextInterface )
        {
            $file = $context->getViewPath() . DIRECTORY_SEPARATOR . $view;
        }
        elseif ( ($currentViewFile = $this->getRequestedViewFile()) !== false )
        {
            $file = dirname($currentViewFile) . DIRECTORY_SEPARATOR . $view;
        }
        else
        {
            throw new InvalidCallException("Unable to resolve view file for view '$view': no active view context.");
        }

        if ( pathinfo($file, PATHINFO_EXTENSION) !== '' )
        {
            return $file;
        }
        $path = $file . '.' . $this->defaultExtension;

        // 11/05/2022 - Remove these lines to allow to use ".tpl.php" extension and override
        // if ( $this->defaultExtension !== 'php' && ! is_file($path) )
        // {
        //     $path = $file . '.php';
        // }

        return $path;
    }


    /**
     * Register CSS files needed for Dezero Backend
     */
    public function registerCssBackend(bool $is_unified = true) : void
    {
        // Get URL where backend assets are published
        $assets_url = Yii::$app->backendManager->assetUrl();

        // CSS files needed for the backend theme
        $vec_css_files = Yii::$app->backendManager->cssFiles($is_unified);
        if ( ! empty($vec_css_files) )
        {
            foreach ( $vec_css_files as $css_file )
            {
                $this->registerCssFile($assets_url . $css_file);
            }
        }
    }


    /**
     * Register Javascript files and variables needed for Dezero Backend
     */
    public function registerJsBackend(bool $is_unified = true) : void
    {
        // Get URL where backend assets are published
        $assets_url = Yii::$app->backendManager->assetUrl();

        // Javascript FILES needed for the backend theme
        $vec_javascript_files = Yii::$app->backendManager->javascriptFiles($is_unified);
        if ( ! empty($vec_javascript_files) )
        {
            foreach ( $vec_javascript_files as $javacript_file )
            {
                if ( is_array($javacript_file) )
                {
                    $this->registerJsFile($assets_url . $javacript_file[0], ['position' => $javacript_file[1]]);
                }
                else
                {
                    $this->registerJsFile($assets_url . $javacript_file);
                }
            }
        }

        // Javascript VARIABLES used by the backend theme
        $vec_javascript_variables = Yii::$app->backendManager->javascriptVariables($is_unified);

        // Include "js_globals" in "params.php" file
        $vec_params = Yii::$app->params;
        if ( isset($vec_params['js_globals']) )
        {
            $vec_javascript_variables = ArrayHelper::merge($vec_javascript_variables, $vec_params['js_globals']);
        }

        if ( ! empty($vec_javascript_variables) )
        {
            $this->registerJs(
                'window.js_globals = ' . Json::encode($vec_javascript_variables) . ';',
                self::POS_END,
                'dezero-backend-variables',
            );
        }
    }
}
