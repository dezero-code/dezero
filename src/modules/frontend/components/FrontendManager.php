<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\frontend\components;

use dezero\base\File;
use dezero\helpers\Url;
use dezero\modules\web\View;
use Dz;
use Yii;
use yii\base\Component;

/**
 * FrontendManager - Helper classes collection for frontend theme
 */
class FrontendManager extends Component
{
    /**
     * @var string Source path where APP frontend assets are stored
     */
    public $source_path = '@frontendTheme/assets';


    /**
     * @var string Path where APP frontend assets are published
     */
    private $asset_path;


    /**
     * @var string URL where APP frontend assets are published
     */
    private $asset_url;


    /**
     * @var string Current controller name
     */
    public $current_controller;


    /**
     * @var string Current action name
     */
    public $current_action;


    /**
     * @var string Current module name
     */
    public $current_module;


    /**
     * Init function. Required!
     */
    public function init()
    {
        parent::init();

        if ( Dz::currentTheme() === 'frontend' )
        {
            $this->current_controller = Dz::currentController(true);
            $this->current_action = Dz::currentAction(true);
            $this->current_module = Dz::currentModule(true);
        }
    }


    /**
     * Return params used on main layout and partials "_header.tpl.php" and "_footer.tpl.php"
     */
    public function layoutParams() : array
    {
        return [
            'current_module'        => Dz::currentModule(true),
            'current_controller'    => Dz::currentController(true),
            'current_action'        => Dz::currentAction(true),
            'language_id'           => Yii::$app->language,
            'is_logged_in'          => ! Yii::$app->user->isGuest,
            'path_info'             => Yii::$app->request->getPathInfo(),
        ];
    }


    /**
     * Return APP published asset URL
     */
    public function assetUrl() : string
    {
        if ( empty($this->asset_url) )
        {
            list($this->asset_path, $this->asset_url) = Yii::$app->assetManager->publish($this->source_path);
            $this->asset_url = Url::to($this->asset_url);
        }

        return $this->asset_url;
    }


    /**
     * Return APP published asset URL
     */
    public function assetPath() : string
    {
        if ( empty($this->asset_path) )
        {
            $asset_url = $this->assetUrl();
        }

        return $this->asset_path;
    }


    /**
     * Return CORE CSS files needed for the frontend theme
     *
     * These files are placed on {@app/themes/frontend/css}
     */
    public function cssFiles(bool $is_minified = true) : array
    {
        if ( $is_minified )
        {
            return [
                '/css/app.min.css'
            ];
        }

        return [
            '/css/app.min.css'
        ];
    }


    /**
     * Return tje JS files needed for the frontend theme
     *
     * These files are placed on {@app/themes/frontend/js}
     */
    public function javascriptFiles(bool $is_minified = true) : array
    {
        if ( $is_minified )
        {
            return [
                '/js/main.bundle.js'
            ];
        }

        return [
            '/js/main.bundle.js'
        ];
    }


    /**
     * Return Javascript files and variables needed for the frontend theme
     */
    public function javascriptVariables() : array
    {
        $vec_variables = [
            'baseUrl'           => Url::base(true),
            'rawBaseUrl'        => Url::base(true),
            'currentUrl'        => Yii::$app->request->hostInfo . Yii::$app->request->url,
            'module'            => Dz::currentModule(true),
            'controller'        => Dz::currentController(true),
            'action'            => Dz::currentAction(true),
            'language'          => Dz::currentLanguage(),
            'defaultLanguage'   => Dz::defaultLanguage(),
            'login_url'         => Url::to(['/user/login']),
            'user_id'           => Yii::$app->user->isGuest ? null : Yii::$app->user->id,
            'full_name'         => Yii::$app->user->isGuest ? '' : Yii::$app->user->identity->fullName(),
        ];

        // Add the language prefix to base URL
        if ( Dz::isMultilanguage() && $this->variables['language'] !== $this->variables['defaultLanguage'] )
        {
            $this->variables['baseUrl'] .= '/' . $this->variables['language'];
        }

        return $vec_variables;
    }

    /**
     * Get body classes
     */
    public function bodyClasses() : array
    {
        $vec_classes = [
            // Current action
            $this->current_action . '-page',

            // Current controller and action
            $this->current_controller . '-' . $this->current_action . '-page',

            // Logged in?
            // ( Yii::$app->user->isGuest ? 'not-logged-in' : 'logged-in')
        ];

        // Add current module
        if ( $this->current_module )
        {
            $vec_classes[] = $this->current_module .'-module-page';
        }

        // Add environment into body class attribute
        $vec_classes[] = Dz::getEnvironment() ."-mode";

        return $vec_classes;
    }


    /**
     * Return the timestamp of an asset file for the URL (cache busting)
     *
     * This method is used on View::registerCssFrontend() and View::registerJsFrontend()
     */
    public function getAssetTimestamp(string $asset_file_name) : string
    {
        // Check if AssetManager::appendTimestamp is enabled
        if ( ! Yii::$app->assetManager->appendTimestamp )
        {
            return '';
        }

        $asset_file = File::load($this->source_path . $asset_file_name);
        if ( ! $asset_file )
        {
            return '';
        }

        $timestamp = $asset_file->updatedDate();
        if ( $timestamp === null )
        {
            return '';
        }

        return '?v=' . $timestamp;
    }
}
