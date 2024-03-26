<?php
/**
 * Base Module class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\base;

use Dz;
use Yii;

/**
 * Module is the base class for module and application classes.
 */
class Module extends \yii\base\Module
{
    /**
    * Initializes the module.
    */
    public function init()
    {
        parent::init();

        // Define alias starting with "@"
        $module_path = Yii::getAlias('@' . $this->id);
        if ( empty($module_path) )
        {
            $module_path = DZ_BASE_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'modules' . DIRECTORY_SEPARATOR . $this->id;
            Yii::setAlias('@' . $this->id, $module_path);
        }

        // Add module into theme to allow their views to be overrided
        if ( Dz::isWeb() )
        {
            Yii::$app->view->theme->addModule($this->id);
        }
    }


    /**
     * Load and switch to a given theme name
     */
    public function loadTheme(string $theme_name) : void
    {
        // Change view class
        Yii::$container->set("dezero\web\View", "{$theme_name}\web\View");

        Yii::$app->view->theme = new \dezero\base\Theme([
            'name' => $theme_name,
            'basePath' => "@app/themes/{$theme_name}",
            'baseUrl' => "@web/themes/{$theme_name}",
        ]);

        // Change layout path
        $layout_path = Yii::getAlias('@app') . "/themes/{$theme_name}/layouts";
        Yii::$app->setLayoutPath($layout_path);
        $this->setLayoutPath($layout_path);

        // Change view path
        $view_path = DZ_APP_PATH . DIRECTORY_SEPARATOR . "themes" . DIRECTORY_SEPARATOR . $theme_name;
        Yii::$app->setViewPath($view_path);
        $this->setViewPath($view_path);
    }
}
