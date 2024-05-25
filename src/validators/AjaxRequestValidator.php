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

    public function __construct(Model|array $model)
    {
        $this->model = $model;
    }

    public function validate() : bool
    {
        if ( ! Yii::$app->request->isAjax )
        {
            return false;
        }

        // Array of models given?
        if ( is_array($this->model) )
        {
            foreach ( $this->model as $model_item )
            {
                if ( ! $model_item->load(Yii::$app->request->post()) )
                {
                    return false;
                }
            }
        }
        else if ( ! $model_item->load(Yii::$app->request->post()) )
        {
            return false;
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        // Validate model. If it's an array of models, validate all of them
        $result = is_array($this->model) ? ActiveForm::validate(...$this->model) : ActiveForm::validate($this->model);

        Yii::$app->response->data = $result;
        Yii::$app->response->send();
        Yii::$app->end();

        return $result;
    }
}
