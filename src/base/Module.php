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

        // Add module into theme to allow their views to be overrided
        Yii::$app->view->theme->addBackendModule($this->id);
    }
}
