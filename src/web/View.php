<?php
/**
 * View represents a view object in the MVC pattern.
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\web;

use Yii;

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

        // 11/05/2022 --> Remove these lines to allow to use ".tpl.php" extension and override
        // if ($this->defaultExtension !== 'php' && !is_file($path)) {
        //     $path = $file . '.php';
        // }

        return $path;
    }
}
