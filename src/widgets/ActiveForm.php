<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\widgets;

use dezero\helpers\Html;
use Yii;

/**
 * ActiveForm is a widget that builds an interactive HTML form for one or multiple data models.
 */
class ActiveForm extends \yii\widgets\ActiveForm
{
    /**
     * @var bool whether to enable client-side data validation.
     * If [[ActiveField::enableClientValidation]] is set, its value will take precedence for that input field.
     */
    public $enableClientValidation = false; // true;


    /**
     * @var bool whether to perform validation when the value of an input field is changed.
     */
    public $validateOnChange = false;   // true;


    /**
     * @var bool whether to perform validation when an input field loses focus.
     * If [[ActiveField::$validateOnBlur]] is set, its value will take precedence for that input field.
     */
    public $validateOnBlur = false; // true;


    /**
     * @var bool whether to perform validation while the user is typing in an input field.
     */
    public $validateOnType = false;


    /**
     * @var string the default CSS class for the error summary container.
     * @see errorSummary()
     */
    public $errorSummaryCssClass = 'messages messages-error alert alert-dismissible dark summary-errors alert-danger';


    /**
     * @var string the CSS class that is added to a field container when the associated attribute has validation error.
     */
    public $errorCssClass = 'has-danger';   // 'has-error';


    /**
     * @var string the default field class name when calling [[field()]] to create a new field.
     * @see fieldConfig
     */
    public $fieldClass = 'dezero\widgets\ActiveField'; // 'yii\widgets\ActiveField';


    /**
     * @var array the HTML attributes (name-value pairs) for the form tag.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $options = [
      'autocomplete'    => 'off'
    ];


    /**
     * {@inheritdoc}
     */
    public function errorSummary($models, $options = [])
    {
        Html::addCssClass($options, $this->errorSummaryCssClass);
        $options['encode'] = $this->encodeErrorSummary;
        return Html::errorSummary($models, $options);
    }
}
