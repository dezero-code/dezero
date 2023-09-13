<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use Dz;
use Yii;

class ErrorAction extends \yii\web\ErrorAction
{
    /**
     * Runs the action.
     *
     * @return string result content
     */
    public function run()
    {
        // Special response for API module (REST API)
        $path_info = Yii::$app->request->getPathInfo();
        if ( ! preg_match("/^api\/v/", $path_info) )
        {
            return parent::run();
        }

        // Prepare response for API module
        if ( $this->layout !== null )
        {
            $this->controller->layout = $this->layout;
        }

        // Force JSON
        Yii::$app->getResponse()->format = \yii\web\Response::FORMAT_JSON;
        Yii::$app->getResponse()->charset = 'UTF-8';
        Yii::$app->getResponse()->setStatusCodeByException($this->exception);

        return $this->renderJsonResponse();
    }


    /**
     * Builds JSON that represents the exception.
     */
    protected function renderJsonResponse()
    {
        return [
            'name'      => $this->getExceptionName(),
            'code'      => $this->getExceptionCode(),
            'message'   => $this->getExceptionMessage(),
            'module'    => 'api'
        ];
    }
}
