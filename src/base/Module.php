<?php
/**
 * Base Module class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\base;

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

        // Allow override views:
        //  - First option is "'@app/themes/backend/<module_id>'
        //  - Second option is "@module/views"
        $module_theme_path = Yii::$app->view->theme->basePath .'/'. $this->id;
        Yii::$app->view->theme->pathMap[$module_theme_path] = [
            $module_theme_path,         // '@app/themes/backend/<module_id>'
            "@{$this->id}/views",       // '@module/views'
        ];
    }
}
