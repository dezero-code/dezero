<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\helpers\Url;
use Yii;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 */
class ActionColumn extends \yii\grid\ActionColumn
{
    /**
     * {@inheritdoc}
     */
    public $headerOptions = ['class' => 'button-column'];


    /**
     * @var array button icons. The array keys are the icon names and the values the corresponding html:
     */
    public $icons = [
        'eye-open' => '<i class="wb-eye"></i>',
        'pencil' => '<i class="wb-edit"></i>',
        'trash' => '<i class="wb-trash"></i>',
    ];


    /**
     * @var array html options to be applied to the [[initDefaultButton()|default button]].
     * @since 2.0.4
     */
    public $buttonOptions = [
        'class' => 'btn btn-sm btn-icon btn-pure btn-default'
    ];


    /**
     * {@inheritdoc}
     */
    protected function renderFilterCellContent()
    {
        // $this->filterOptions = ArrayHelper::merge(['class' => $this->filterAttribute .'_filter'], $this->filterOptions);
        // return parent::renderFilterCell();
        return '<a class="clear btn btn-default" id="'. $this->grid->options['id'] .'-clear-btn" style="text-align:center;display:block;" data-toggle="tooltip" href="'. Url::canonical() .'?clear=1" data-original-title="'. Yii::t('backend', 'Clear filters') .'"><i class="wb-close"></i></a>';
    }


    /**
     * {@inheritdoc}
     */
    public function renderDataCell($model, $key, $index)
    {
        $vec_default_options = [
            'class' => 'button-column',
            'data-grid' => $this->grid->options['id']
        ];
        $this->contentOptions = ArrayHelper::merge($vec_default_options, $this->contentOptions);
        return parent::renderDataCell($model, $key, $index);
    }


    /**
     * {@inheritdoc}
     */
    protected function initDefaultButtons()
    {
        parent::initDefaultButtons();

        $this->initDefaultButton('delete-ajax', 'trash', [
            'data-ajax-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
            // 'data-method' => 'ajax',
        ]);
    }


    /**
     * {@inheritdoc}
     */
    protected function initDefaultButton($name, $iconName, $additionalOptions = [])
    {
        if ( !isset($this->buttons[$name]) && strpos($this->template, '{' . $name . '}') !== false )
        {
            $this->buttons[$name] = function ($url, $model, $key) use ($name, $iconName, $additionalOptions) {
                switch ($name) {
                    case 'view':
                        $title = Yii::t('yii', 'View');
                        $icon = 'eye';
                    break;
                    case 'update':
                        $title = Yii::t('yii', 'Update');
                        $icon = 'edit';
                    break;
                    case 'delete':
                    case 'delete-ajax':
                        $title = Yii::t('yii', 'Delete');
                        $icon = 'trash';
                    break;
                    default:
                        $title = ucfirst($name);
                        $icon = '';
                    break;
                }

                $options = array_merge([
                    'title' => $title,
                    'aria-label' => $title,
                    'data-pjax' => '0',
                ], $additionalOptions, $this->buttonOptions);
                if ( !empty($icon) )
                {
                    $options['icon'] = $icon;
                }

                // Add action class
                $options['class'] = $options['class'] ?? '';
                $options['class'] .= " {$name}-action";

                // Special class for delete button
                // if ( $name === 'delete' )
                // {
                //     $options['class'] = $options['class'] ?? '';
                //     $options['class'] .= ' dz-bootbox-confirm';
                // }

                return Html::gridButton($title, $url, $options);
            };
        }
    }
}
