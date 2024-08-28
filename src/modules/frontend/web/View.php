<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\frontend\web;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use Yii;
use yii\base\InvalidCallException;

/**
 * View represents a view object in the MVC pattern.
 */
class View extends \dezero\web\View
{
    /**
     * Register CSS files needed for frontend
     */
    public function registerCssFrontend(bool $is_unified = true) : void
    {
        // Get URL where APP frontend assets are published
        $assets_url = Yii::$app->frontendManager->assetUrl();

        // CSS files needed for the frontend theme
        $vec_css_files = Yii::$app->frontendManager->cssFiles($is_unified);
        if ( ! empty($vec_css_files) )
        {
            foreach ( $vec_css_files as $css_file )
            {
                $timestamp = Yii::$app->frontendManager->getAssetTimestamp($css_file);
                $this->registerCssFile($assets_url . $css_file . $timestamp);
            }
        }
    }


    /**
     * Register Javascript files and variables needed for frontend
     */
    public function registerJsFrontend(bool $is_unified = true) : void
    {
        // Get URL where frontend assets are published
        $assets_url = Yii::$app->frontendManager->assetUrl();

        // Javascript FILES needed for the frontend theme
        $vec_javascript_files = Yii::$app->frontendManager->javascriptFiles($is_unified);
        if ( ! empty($vec_javascript_files) )
        {
            foreach ( $vec_javascript_files as $javacript_file )
            {
                if ( is_array($javacript_file) )
                {
                    $timestamp = Yii::$app->frontendManager->getAssetTimestamp($javacript_file[0]);
                    $this->registerJsFile($assets_url . $javacript_file[0] . $timestamp, ['position' => $javacript_file[1]]);
                }
                else
                {
                    $timestamp = Yii::$app->frontendManager->getAssetTimestamp($javacript_file);
                    $this->registerJsFile($assets_url . $javacript_file . $timestamp, ['position' => self::POS_END]);
                }
            }
        }


        // -------------------------------------------------------------------------
        // JAVASCRIPT VARIABLES
        // -------------------------------------------------------------------------

        // Javascript VARIABLES used by the frontend theme
        $vec_javascript_variables = Yii::$app->frontendManager->javascriptVariables($is_unified);

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
                'dezero-frontend-variables',
            );
        }
    }
}
