<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\validators;

use dezero\contracts\ValidatorInterface;
use Yii;
use yii\base\Model;
use yii\web\Response;
use yii\widgets\ActiveForm;

/**
 * Implements model validation process via AJAX requests
 */
class AjaxRequestValidator implements ValidatorInterface
{
    protected $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function validate() : bool
    {
        if ( ! Yii::$app->request->isAjax || ! $this->model->load(Yii::$app->request->post()) )
        {
            return false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        $result = ActiveForm::validate($this->model);

        Yii::$app->response->data = $result;
        Yii::$app->response->send();
        Yii::$app->end();

        return $result;
    }
}
