<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 */
class ActionColumn extends \yii\grid\ActionColumn
{
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
}
