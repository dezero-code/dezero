<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

use dezero\helpers\ArrayHelper;

/**
 * ActionColumn is a column for the [[GridView]] widget that displays buttons for viewing and manipulating the items.
 */
class DataColumn extends \yii\grid\DataColumn
{
    /**
     * {@inheritdoc}
     */
    public function renderHeaderCell()
    {
        $this->headerOptions = ArrayHelper::merge(['class' => $this->attribute .'_header'], $this->headerOptions);
        return parent::renderHeaderCell();
    }


    /**
     * {@inheritdoc}
     */
    public function renderFilterCell()
    {
        $this->filterOptions = ArrayHelper::merge(['class' => $this->filterAttribute .'_filter'], $this->filterOptions);
        return parent::renderFilterCell();
    }


    /**
     * {@inheritdoc}
     */
    public function renderDataCell($model, $key, $index)
    {
        $this->contentOptions = ArrayHelper::merge(['class' => $this->attribute .'_column'], $this->contentOptions);
        return parent::renderDataCell($model, $key, $index);
    }


    /**
     * {@inheritdoc}
     */
    public function renderFooterCell()
    {
        $this->footerOptions = ArrayHelper::merge(['class' => $this->attribute .'_footer'], $this->footerOptions);
        return parent::renderFooterCell();
    }
}
