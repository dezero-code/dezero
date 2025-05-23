<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\system\components;

use dezero\base\File;
use dezero\helpers\Url;
use dezero\web\View;
use Dz;
use Yii;
use yii\base\Component;

/**
 * BackendManager - Helper classes collection for backend theme
 */
class BackendManager extends Component
{
    /**
     * @var string Source path where CORE backend assets are stored
     */
    public $core_source_path = '@core/assets';


    /**
     * @var string Path where CORE backend assets are published
     */
    private $core_asset_path;


    /**
     * @var string URL where CORE backend assets are published
     */
    private $core_asset_url;


    /**
     * @var string Source path where APP backend assets are stored
     */
    public $app_source_path = '@backend/assets';


    /**
     * @var string Path where APP backend assets are published
     */
    private $app_asset_path;


    /**
     * @var string URL where APP backend assets are published
     */
    private $app_asset_url;


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

        if ( Dz::currentTheme() === 'backend' )
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

        // Login & password page (different layout)
        if ( Yii::$app->user->isGuest || ($this->current_module == 'user' && $this->current_controller == 'password') )
        {
            $vec_classes[] = 'page-login';
            $vec_classes[] = 'layout-full';
        }

        // Logged-in pages -> Enable main menu
        else
        {
            $vec_classes[] = 'site-navbar-small';

            // Left sidebar column
            if ( $this->current_module == 'admin' && ( $this->current_controller == 'mail' && $this->current_action !== 'view' ) )
            {
                $vec_classes[] = 'page-aside-static';
                $vec_classes[] = 'page-aside-left';
            }
        }

        return $vec_classes;
    }


    /**
     * Return CORE published asset URL
     */
    public function coreAssetUrl() : string
    {
        if ( empty($this->core_asset_url) )
        {
            list($this->core_asset_path, $this->core_asset_url) = Yii::$app->assetManager->publish($this->core_source_path);
            $this->core_asset_url = Url::to($this->core_asset_url);
        }

        return $this->core_asset_url;
    }


    /**
     * Return CORE published asset URL
     */
    public function coreAssetPath() : string
    {
        if ( empty($this->core_asset_path) )
        {
            $core_asset_url = $this->coreAssetUrl();
        }

        return $this->core_asset_path;
    }


    /**
     * Return APP published asset URL
     */
    public function assetUrl() : string
    {
        if ( empty($this->app_asset_url) )
        {
            list($this->app_asset_path, $this->app_asset_url) = Yii::$app->assetManager->publish($this->app_source_path);
            $this->app_asset_url = Url::to($this->app_asset_url);
        }

        return $this->app_asset_url;
    }


    /**
     * Return APP published asset URL
     */
    public function assetPath() : string
    {
        if ( empty($this->app_asset_path) )
        {
            $app_asset_url = $this->assetUrl();
        }

        return $this->app_asset_path;
    }


    /**
     * Return CORE CSS files needed for the backend theme
     *
     * These files are placed on {@core/assets/css}
     */
    public function coreCssFiles(bool $is_unified = true) : array
    {
        $vec_files = [];

        // Unify CSS files?
        if ( $is_unified )
        {
            // CSS - Dezero framework
            $vec_files[] = '/css/dezero-core.min.css';
        }

        // Separated CSS files?
        else
        {
            // CSS - Remark & core
            $vec_files[] = '/libraries/_remark/global/css/bootstrap.min.css';
            $vec_files[] = '/libraries/_remark/global/css/bootstrap-extend.min.css';
            $vec_files[] = '/libraries/_remark/assets/css/site.min.css';

            // CSS - Libraries / plugins
            $vec_files[] = '/libraries/animsition/animsition.min.css';
            $vec_files[] = '/libraries/jquery-mmenu/jquery-mmenu.min.css';
            $vec_files[] = '/libraries/select2/select2.min.css';
            $vec_files[] = '/libraries/asscrollable/asScrollable.min.css';
            $vec_files[] = '/libraries/pnotify/jquery.pnotify.css';
            $vec_files[] = '/libraries/bootstrap-datepicker/bootstrap-datepicker.min.css';
            $vec_files[] = '/libraries/bootstrap-touchspin-4/jquery.bootstrap-touchspin.min.css';
            $vec_files[] = '/libraries/bootstrap-tokenfield/bootstrap-tokenfield.min.css';

            // CSS - Fonts
            $vec_files[] = '/fonts/web-icons/web-icons.min.css';

            // CSS - Dezero framework
            $vec_files[] = '/css/style.min.css';
        }

        return $vec_files;
    }


    /**
     * Return CORE CSS files needed for the backend theme
     *
     * These files are placed on {@app/themes/backend/css}
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
     * Return CORE Javascript files needed for the backend theme
     *
     * These files are placed on {@core/assets/js}
     */
    public function coreJavascriptFiles(bool $is_unified = true) : array
    {
        $vec_files = [];

        // Unify JS files?
        if ( $is_unified )
        {
            // Jquery
            $vec_files[] = ['/libraries/jquery/jquery.min.js', View::POS_HEAD];

            // JS - Dezero CORE Framework
            $vec_files[] = ['/js/dezero-core.min.js', View::POS_END];
            // $vec_files[] = ['/js/dezero-core.min.js', View::POS_END];
        }

        // Separated JS files?
        else
        {
            // Javascript - CORE
            $vec_files[] = ['/libraries/jquery/jquery.min.js', View::POS_HEAD];
            $vec_files[] = ['/libraries/babel-external-helpers/babel-external-helpers.js', View::POS_END];
            $vec_files[] = ['/libraries/tether/tether.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap/bootstrap.min.js', View::POS_END];
            $vec_files[] = ['/libraries/animsition/animsition.min.js', View::POS_END];
            $vec_files[] = ['/libraries/mousewheel/jquery.mousewheel.min.js', View::POS_END];
            $vec_files[] = ['/libraries/asscrollbar/jquery-asScrollbar.min.js', View::POS_END];
            $vec_files[] = ['/libraries/asscrollable/jquery-asScrollable.min.js', View::POS_END];

            // Javascript - Libraries / plugins
            $vec_files[] = ['/libraries/jquery-mmenu/jquery.mmenu.min.all.js', View::POS_END];
            $vec_files[] = ['/libraries/select2/select2.full.min.js', View::POS_END];
            $vec_files[] = ['/libraries/scrollto/jquery.scrollTo.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootbox/jquery.bootbox.min.js', View::POS_END];
            $vec_files[] = ['/libraries/pnotify/jquery.pnotify.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap-datepicker/bootstrap-datepicker.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap-datepicker/bootstrap-datepicker.es.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap-touchspin-4/jquery.number.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap-touchspin-4/jquery.bootstrap-touchspin.min.js', View::POS_END];
            $vec_files[] = ['/libraries/bootstrap-tokenfield/bootstrap-tokenfield.min.js', View::POS_END];

            $vec_files[] = ['/libraries/_remark/global/js/State.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Component.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Plugin.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Base.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Config.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/assets/js/Section/Menubar.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/assets/js/Section/Sidebar.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/assets/js/Section/PageAside.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/assets/js/Section/GridMenu.min.js', View::POS_END];

            $vec_files[] = ['/libraries/_remark/global/js/config/colors.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/assets/js/config/tour.min.js', View::POS_END];

            $vec_files[] = ['/libraries/_remark/assets/js/Site.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Plugin/asscrollable.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Plugin/select2.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Plugin/bootstrap-datepicker.min.js', View::POS_END];
            $vec_files[] = ['/libraries/_remark/global/js/Plugin/bootstrap-touchspin-4.js', View::POS_END];

            // Custom Javascript
            $vec_files[] = ['/js/dz.ajaxgrid.js', View::POS_END];
            $vec_files[] = ['/js/scripts.js', View::POS_END];
        }

        return $vec_files;
    }


    /**
     * Return CORE JS files needed for the backend theme
     *
     * These files are placed on {@app/themes/backend/js}
     */
    public function javascriptFiles(bool $is_minified = true) : array
    {
        if ( $is_minified )
        {
            return [
                '/js/app.min.js'
            ];
        }

        return [
            '/js/app.js'
        ];
    }


    /**
     * Return Javascript files and variables needed for the backend theme
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
        ];

        // Add the language prefix to base URL
        if ( Dz::isMultilanguage() && $this->variables['language'] !== $this->variables['defaultLanguage'] )
        {
            $this->variables['baseUrl'] .= '/' . $this->variables['language'];
        }

        return $vec_variables;
    }


    /**
     * Return the timestamp of an asset file for the URL (cache busting)
     *
     * This method is used on View::registerCssBackend() and View::registerJsBackend()
     */
    public function getAssetTimestamp(string $asset_file_name, bool $is_core = false) : string
    {
        // Check if AssetManager::appendTimestamp is enabled
        if ( ! Yii::$app->assetManager->appendTimestamp )
        {
            return '';
        }

        // Get the asset file path (app or core)
        $asset_file_path = $this->app_source_path . $asset_file_name;
        if ( $is_core )
        {
            $asset_file_path = $this->core_source_path . $asset_file_name;
        }

        $asset_file = File::load($asset_file_path);
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


    /**
     * Return the timestamp of a CORE asset file for the URL (cache busting)
     *
     * This method is used on View::registerCssBackend() and View::registerJsBackend()
     */
    public function getCoreAssetTimestamp(string $asset_file_name) : string
    {
        return $this->getAssetTimestamp($asset_file_name, true);
    }
}
