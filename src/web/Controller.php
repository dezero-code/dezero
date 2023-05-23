<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\validators\AjaxRequestValidator;
use Dz;
use Yii;

/**
 * Controller is the base class of web controllers.
 */
class Controller extends \yii\web\Controller
{
    /**
     * Validate model via AJAX
     */
    protected function validateAjaxRequest($model)
    {
        Dz::makeObject(AjaxRequestValidator::class, [$model])->validate();
    }
}
