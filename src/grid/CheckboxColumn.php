<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

use Closure;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\helpers\Json;
use Yii;

/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 */
class CheckboxColumn extends \yii\grid\CheckboxColumn
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        // By default, use "{grid_id}-checkbox" as name attribute
        if ( $this->name === 'selection' )
        {
            $this->name = "{$this->grid->id}-checkbox";
        }

        // By default, use "checkbox-gridview" as default class
        if ( $this->cssClass === null )
        {
            $this->cssClass = 'checkbox-gridview';
        }

        parent::init();
    }


    /**
     * {@inheritdoc}
     */
    protected function renderHeaderCellContent()
    {
        if ( $this->header !== null || ! $this->multiple ) {
            return parent::renderHeaderCellContent();
        }

        $vec_options = [
            'class' => 'select-on-check-all',
            'id'    => strtr($this->name, ['[]' => '']) .'-all'
        ];

        return '<div class="checkbox-custom checkbox-primary checkbox-lg">'. Html::checkbox($this->getHeaderCheckBoxName(), false, $vec_options) .'<label for="'. $vec_options['id'] .'"></label></div>';
    }


    /**
     * {@inheritdoc}
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        if ( $this->content !== null )
        {
            return parent::renderDataCellContent($model, $key, $index);
        }

        if ( $this->checkboxOptions instanceof Closure )
        {
            $vec_options = call_user_func($this->checkboxOptions, $model, $key, $index, $this);
        }
        else
        {
            $vec_options = $this->checkboxOptions;
        }

        if ( !isset($vec_options['value']) )
        {
            $vec_options['value'] = is_array($key) ? Json::encode($key) : $key;
        }

        if ( $this->cssClass !== null )
        {
            Html::addCssClass($vec_options, $this->cssClass);
        }

        $vec_options['id'] = strtr($this->name, ['[]' => '']) .'-'. $index;

        return '<div class="checkbox-custom checkbox-primary checkbox-lg">'. Html::checkbox($this->name, !empty($vec_options['checked']), $vec_options) .'<label for="'. $vec_options['id'] .'"></label></div>';
    }
}
