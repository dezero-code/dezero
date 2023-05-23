<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\widgets;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\widgets\ActiveForm;
use Yii;

/**
 * ActiveField represents a form input field within an [[ActiveForm]].
 */
class ActiveField extends \yii\bootstrap4\ActiveField
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


    /**
     * {@inheritdoc}
     */
    protected function createLayoutConfig($instanceConfig)
    {
        // Custom configuration form layouts for Dezero Framework theme
        $config = [
            'hintOptions' => [
                'tag'   => 'p',             // 'tag' => 'small',
                'class' => 'help-block',    // 'class' => ['form-text', 'text-muted'],
            ],
            'errorOptions' => [
                'tag' => 'span',
                'class' => 'help-inline text-help text-danger',
            ],
            'inputOptions' => [
                'class' => 'form-control'
            ],
            'labelOptions' => [
                'class' => []
            ]
        ];

        $layout = $instanceConfig['form']->layout;


        if ( $layout === ActiveForm::LAYOUT_HORIZONTAL )
        {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{hint}\n{endWrapper}";
            $config['wrapperOptions'] = [];
            $config['labelOptions'] = [];
            $config['options'] = [];
            $cssClasses = [
                'offset' => ['col-sm-8', 'offset-sm-4'],
                'label' => ['col-sm-4', 'form-control-label'],
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => ['help-block'],
                'field' => 'form-group row'
            ];
            if ( isset($instanceConfig['horizontalCssClasses']) )
            {
                $cssClasses = ArrayHelper::merge($cssClasses, $instanceConfig['horizontalCssClasses']);
            }
            $config['horizontalCssClasses'] = $cssClasses;

            Html::addCssClass($config['wrapperOptions'], $cssClasses['wrapper']);
            Html::addCssClass($config['labelOptions'], $cssClasses['label']);
            Html::addCssClass($config['errorOptions'], $cssClasses['error']);
            Html::addCssClass($config['hintOptions'], $cssClasses['hint']);
            Html::addCssClass($config['options'], $cssClasses['field']);
        }
        elseif ( $layout === ActiveForm::LAYOUT_INLINE )
        {
            $config['inputOptions']['placeholder'] = true;
            $config['enableError'] = false;

            Html::addCssClass($config['labelOptions'], ['screenreader' => 'sr-only']);
        }

        return $config;
    }
}
