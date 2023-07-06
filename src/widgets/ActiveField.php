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
     * @var array the default options for the input checkboxes. The parameter passed to individual
     * input methods (e.g. [[checkbox()]]) will be merged with this property when rendering the input tag.
     */
    public $checkOptions = [
        'class' => ['widget' => ''],
        'labelOptions' => [
            'class' => ['widget' => '']
        ]
    ];


    /**
     * @var array the default options for the input radios. The parameter passed to individual
     * input methods (e.g. [[radio()]]) will be merged with this property when rendering the input tag.
     */
    public $radioOptions = [
        'class' => ['widget' => ''],
        'labelOptions' => [
            'class' => ['widget' => '']
        ]
    ];


    /**
     * @var null|array CSS grid classes for horizontal layout. This must be an array with these keys:
     *  - 'offset' the offset grid class to append to the wrapper if no label is rendered
     *  - 'label' the label grid class
     *  - 'wrapper' the wrapper grid class
     *  - 'error' the error grid class
     *  - 'hint' the hint grid class
     */
    public $columns = [];


    /*
    |--------------------------------------------------------------------------
    | OVERRIDED METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * {@inheritdoc}
     */
    protected function createLayoutConfig($instance_config)
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
                'class' => 'form-control-label'
            ]
        ];

        $layout = $instance_config['form']->layout;

        // Custom option "columns" (alias of horizontalCssClasses)
        if ( isset($instance_config['columns']) && !isset($instance_config['horizontalCssClasses']) )
        {
            $instance_config['horizontalCssClasses'] = $instance_config['columns'];
        }

        if ( $layout === ActiveForm::LAYOUT_HORIZONTAL )
        {
            $config['template'] = "{label}\n{beginWrapper}\n{input}\n{error}\n{hint}\n{endWrapper}";
            $config['wrapperOptions'] = [];
            $config['labelOptions'] = [];
            $config['options'] = [];
            $vec_css_classes = [
                'offset' => ['col-sm-8', 'offset-sm-4'],
                'label' => 'col-sm-4',
                'wrapper' => 'col-sm-8',
                'error' => '',
                'hint' => ['help-block'],
                'field' => 'form-group row'
            ];
            if ( isset($instance_config['horizontalCssClasses']) )
            {
                $vec_css_classes = ArrayHelper::merge($vec_css_classes, $instance_config['horizontalCssClasses']);
            }
            // Ensure "form-control-label" is added into "label"
            if ( !preg_match("/form\-control\-label/", $vec_css_classes['label']) )
            {
                $vec_css_classes['label'] .= ' form-control-label';
            }
            $config['horizontalCssClasses'] = $vec_css_classes;

            Html::addCssClass($config['wrapperOptions'], $vec_css_classes['wrapper']);
            Html::addCssClass($config['labelOptions'], $vec_css_classes['label']);
            Html::addCssClass($config['errorOptions'], $vec_css_classes['error']);
            Html::addCssClass($config['hintOptions'], $vec_css_classes['hint']);
            Html::addCssClass($config['options'], $vec_css_classes['field']);
        }
        elseif ( $layout === ActiveForm::LAYOUT_INLINE )
        {
            $config['inputOptions']['placeholder'] = true;
            $config['enableError'] = false;

            Html::addCssClass($config['labelOptions'], ['screenreader' => 'sr-only']);
        }

        return $config;
    }


    /**
     * {@inheritdoc}
     */
    public function checkboxList($items, $options = [])
    {
        if ( !isset($options['item']) )
        {
            $this->template = str_replace("\n{error}", '', $this->template);
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $itemCount = count($items) - 1;
            $error = $this->error()->parts['{error}'];

            $options['item'] = function ($i, $label, $name, $checked, $value) use (
                $itemOptions,
                $encode,
                $itemCount,
                $error
            ) {
                $options = array_merge($this->checkOptions, [
                    'label' => $encode ? Html::encode($label) : $label,
                    'value' => $value
                ], $itemOptions);
                // $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['custom-control', 'custom-checkbox']]);
                $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['checkbox-custom', 'checkbox-primary']]);

                if ( $this->inline )
                {
                    Html::addCssClass($wrapperOptions, 'checkbox-inline');
                }

                // Custom "id" attribute for this checkbox item. For example, "user-roles-1"
                if ( !isset($options['id']) )
                {
                    $options['id'] = $this->getInputId() .'-'. Html::getInputIdByName($value);
                }

                $this->addErrorClassIfNeeded($options);
                $html =
                    Html::beginTag('div', $wrapperOptions) . "\n" .
                    Html::checkboxInline($name, $checked, $options) . "\n";

                if ( $itemCount === $i )
                {
                    $html .= $error . "\n";
                }

                $html .= Html::endTag('div') . "\n";

                return $html;
            };
        }

        parent::checkboxList($items, $options);
        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function radioList($items, $options = [])
    {
        if ( ! isset($options['item']) )
        {
            Html::addCssClass($options, 'form-group form-radio-group');

            $this->template = str_replace("\n{error}", '', $this->template);
            $itemOptions = isset($options['itemOptions']) ? $options['itemOptions'] : [];
            $encode = ArrayHelper::getValue($options, 'encode', true);
            $itemCount = count($items) - 1;
            $error = $this->error()->parts['{error}'];

            $options['item'] = function ($i, $label, $name, $checked, $value) use (
                $itemOptions,
                $encode,
                $itemCount,
                $error
            ) {
                $options = array_merge($this->radioOptions, [
                    'label' => $encode ? Html::encode($label) : $label,
                    'value' => $value
                ], $itemOptions);
                // $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['custom-control', 'custom-radio']]);
                $wrapperOptions = ArrayHelper::remove($options, 'wrapperOptions', ['class' => ['radio-custom', 'radio-default']]);

                // Custom inline classs
                if ( $this->inline )
                {
                    Html::addCssClass($wrapperOptions, 'radio-inline');
                }

                // Custom "id" attribute for this radio item. For example, "user-is_force_change_password-1"
                if ( !isset($options['id']) )
                {
                    $options['id'] = $this->getInputId() .'-'. Html::getInputIdByName($value);
                }

                $this->addErrorClassIfNeeded($options);

                // Wrapper output <div class="radio-custom radio-default">{radioInline}</div>
                $html =
                    Html::beginTag('div', $wrapperOptions) . "\n" .
                    Html::radioInline($name, $checked, $options) . "\n";

                if ( $itemCount === $i )
                {
                    $html .= $error . "\n";
                }

                $html .= Html::endTag('div') . "\n";

                return $html;
            };

            $options['unselect'] = null;
        }

        parent::radioList($items, $options);
        return $this;
    }


   /*
    |--------------------------------------------------------------------------
    | CUSTOM METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Renders an email input.
     */
    public function emailInput(?array $options = []) : self
    {
        return $this->input('email', $options);
    }
}
