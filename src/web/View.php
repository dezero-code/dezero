<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\helpers\Json;
use Yii;
use yii\base\InvalidCallException;

/**
 * View represents a view object in the MVC pattern.
 */
class View extends \yii\web\View
{
    /**
     * The location of registered JavaScript code block or files.
     * This means the location is at the end of the body section, but on the top
     */
    const POS_END_TOP = 10;


    /**
     * Finds the view file based on the given view name.
     *
     * {@inheritdoc}
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
     * {@inheritdoc}
     *
     * Added new POS_END_TOP position (at the top of the end)
     *
     * @since 27/03/2024
     */
    protected function renderBodyEndHtml($ajaxMode)
    {
        if ( $ajaxMode || ( !isset($this->jsFiles[self::POS_END_TOP]) && !isset($this->js[self::POS_END_TOP]) ) )
        {
            return parent::renderBodyEndHtml($ajaxMode);
        }


        $lines = [];

        // New position "POS_END_TOP" (at the top of the end)
        if ( isset($this->js[self::POS_END_TOP]) && !empty($this->js[self::POS_END_TOP]) )
        {
            $lines[] = Html::script(implode("\n", $this->js[self::POS_END_TOP]));
        }

        if ( isset($this->jsFiles[self::POS_END_TOP]) &&  !empty($this->jsFiles[self::POS_END_TOP]) )
        {
            $lines[] = implode("\n", $this->jsFiles[self::POS_END_TOP]);
        }

        if ( !empty($this->jsFiles[self::POS_END]) )
        {
            $lines[] = implode("\n", $this->jsFiles[self::POS_END]);
        }

        if ( !empty($this->js[self::POS_END]) )
        {
            $lines[] = Html::script(implode("\n", $this->js[self::POS_END]));
        }

        if ( !empty($this->js[self::POS_READY]) )
        {
            $js = "jQuery(function ($) {\n" . implode("\n", $this->js[self::POS_READY]) . "\n});";
            $lines[] = Html::script($js);
        }

        if ( !empty($this->js[self::POS_LOAD]) )
        {
            $js = "jQuery(window).on('load', function () {\n" . implode("\n", $this->js[self::POS_LOAD]) . "\n});";
            $lines[] = Html::script($js);
        }

        return empty($lines) ? '' : implode("\n", $lines);

    }


    /**
     * Register CSS files needed for Dezero Backend
     */
    public function registerCssBackend(bool $is_unified = true) : void
    {
        // -------------------------------------------------------------------------
        // CORE CSS FILES
        // -------------------------------------------------------------------------

        // Get URL where CORE backend assets are published
        $core_assets_url = Yii::$app->backendManager->coreAssetUrl();

        // CSS files needed for the backend theme
        $vec_css_files = Yii::$app->backendManager->coreCssFiles($is_unified);
        if ( ! empty($vec_css_files) )
        {
            foreach ( $vec_css_files as $css_file )
            {
                $timestamp = Yii::$app->backendManager->getCoreAssetTimestamp($css_file);
                $this->registerCssFile($core_assets_url . $css_file . $timestamp);
            }
        }


        // --------------------------------------------------------------------------
        // APP CSS FILES
        // --------------------------------------------------------------------------

        // Get URL where APP backend assets are published
        $app_assets_url = Yii::$app->backendManager->assetUrl();

        // CSS files needed for the backend theme
        $vec_css_files = Yii::$app->backendManager->cssFiles($is_unified);
        if ( ! empty($vec_css_files) )
        {
            foreach ( $vec_css_files as $css_file )
            {
                $timestamp = Yii::$app->backendManager->getAssetTimestamp($css_file);
                $this->registerCssFile($app_assets_url . $css_file . $timestamp);
            }
        }
    }


    /**
     * Register Javascript files and variables needed for Dezero Backend
     */
    public function registerJsBackend(bool $is_unified = true) : void
    {
        // -------------------------------------------------------------------------
        // CORE JS FILES
        // -------------------------------------------------------------------------

        // Get URL where backend assets are published
        $core_assets_url = Yii::$app->backendManager->coreAssetUrl();

        // Javascript FILES needed for the backend theme
        $vec_javascript_files = Yii::$app->backendManager->coreJavascriptFiles($is_unified);
        if ( ! empty($vec_javascript_files) )
        {
            foreach ( $vec_javascript_files as $javacript_file )
            {
                if ( is_array($javacript_file) )
                {
                    $timestamp = Yii::$app->backendManager->getCoreAssetTimestamp($javacript_file[0]);
                    $this->registerJsFile($core_assets_url . $javacript_file[0] . $timestamp, ['position' => $javacript_file[1]]);
                }
                else
                {
                    $timestamp = Yii::$app->backendManager->getCoreAssetTimestamp($javacript_file);
                    $this->registerJsFile($core_assets_url . $javacript_file . $timestamp);
                }
            }
        }


        // -------------------------------------------------------------------------
        // APP JS FILES
        // -------------------------------------------------------------------------

        // Get URL where backend assets are published
        $app_assets_url = Yii::$app->backendManager->assetUrl();

        // Javascript FILES needed for the backend theme
        $vec_javascript_files = Yii::$app->backendManager->javascriptFiles($is_unified);
        if ( ! empty($vec_javascript_files) )
        {
            foreach ( $vec_javascript_files as $javacript_file )
            {
                if ( is_array($javacript_file) )
                {
                    $timestamp = Yii::$app->backendManager->getAssetTimestamp($javacript_file[0]);
                    $this->registerJsFile($app_assets_url . $javacript_file[0]. $timestamp, ['position' => $javacript_file[1]]);
                }
                else
                {
                    $timestamp = Yii::$app->backendManager->getAssetTimestamp($javacript_file);
                    $this->registerJsFile($app_assets_url . $javacript_file. $timestamp);
                }
            }
        }


        // -------------------------------------------------------------------------
        // JAVASCRIPT VARIABLES
        // -------------------------------------------------------------------------

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
                self::POS_END_TOP,
                'dezero-backend-variables',
            );
        }
    }
}
