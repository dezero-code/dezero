<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\widgets;

use Yii;

/**
 * ActiveField represents a form input field within an [[ActiveForm]].
 */
class ActiveField extends \yii\widgets\ActiveField
{
    /**
     * @var array the HTML attributes (name-value pairs) for the field container tag.
     */
    public $options = ['class' => 'form-group'];


    /**
     * @var string the template that is used to arrange the label, the input field, the error message and the hint text.
     * The following tokens will be replaced when [[render()]] is called: `{label}`, `{input}`, `{error}` and `{hint}`.
     */
    public $template = "{label}\n{input}\n{hint}\n{error}";


    /**
     * @var array the default options for the error tags. The parameter passed to [[error()]] will be
     * merged with this property when rendering the error tag.
     */
    public $errorOptions = ['class' => 'help-inline text-help text-danger'];    // ['help-block']
}
