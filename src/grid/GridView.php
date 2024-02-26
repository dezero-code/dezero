<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

use dezero\helpers\Html;
use dezero\grid\GridViewAsset;
use Yii;

/**
 * The GridView widget is used to display data in a grid.
 */
class GridView extends \yii\grid\GridView
{
    /**
     * @var array the HTML attributes for the grid table element.
     * @see \yii\helpers\Html::renderTagAttributes() for details on how attributes are being rendered.
     */
    public $tableOptions = ['class' => 'table table-striped table-hover'];


    /**
     * @var string the default data column class if the class name is not explicitly specified when configuring a data column.
     * Defaults to 'yii\grid\DataColumn'.
     */
    public $dataColumnClass = 'dezero\grid\DataColumn';

    /**
     * @var string the layout that determines how different sections of the grid view should be organized.
     * The following tokens will be replaced with the corresponding section contents:
     *
     * - `{summary}`: the summary section. See [[renderSummary()]].
     * - `{errors}`: the filter model error summary. See [[renderErrors()]].
     * - `{items}`: the list items. See [[renderItems()]].
     * - `{sorter}`: the sorter. See [[renderSorter()]].
     * - `{pager}`: the pager. See [[renderPager()]].
     */
    public $layout = "{items} {summary} {pager}";


    /**
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'grid-view');
        Html::addCssClass($this->options, 'dz-grid-view');

        parent::init();

        // Use Bootstrap4 pager
        $this->pager = [
            'class'             => 'yii\bootstrap4\LinkPager',
            'firstPageLabel'    => "<span aria-hidden=\"true\">&lt;&lt;</span> ". Yii::t('backend', 'First'),
            'prevPageLabel'     => "<span aria-hidden=\"true\">&lt;</span> ". Yii::t('backend', 'Previous'),
            'nextPageLabel'     => Yii::t('backend', 'Next') ." <span aria-hidden=\"true\">&gt;</span>",
            'lastPageLabel'     => Yii::t('backend', 'Last') ." <span aria-hidden=\"true\">&gt;&gt;</span>",
            'options'           => [
                'id'    => $this->options['id'] .'-pager',
                'class' => 'pagination'
            ],
            'maxButtonCount' => 5
        ];

        // Change summary content
        $this->summary = Yii::t('backend', 'Page {page} of {pageCount}') .'<br>'. Yii::t('backend', 'Showing <b>{begin, number}-{end, number}</b> of <b>{totalCount, number}</b> {totalCount, plural, one{item} other{items}}');
    }


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $view = $this->getView();
        GridViewAsset::register($view);

        $id = $this->options['id'];
        $hash = hash('crc32', $id.'gridview');
        $view->registerJs(";$.dezeroGridview.init('$id', '$hash');");

        parent::run();
    }


    /**
     * {@inheritdoc}
     */
    public function renderItems()
    {
        $id = $this->options['id'];
        $this->tableOptions['id'] = $id .'-table';
        return
            '<div class="dz-loader-overlay"><div class="dz-loader loader loader-circle"></div></div>' .
            '<div class="table-responsive">' . parent::renderItems() .'</div>';
    }


    /**
     * {@inheritdoc}
     */
    public function renderTableRow($model, $key, $index)
    {
        $cells = [];

        /* @var $column Column */
        foreach ( $this->columns as $column )
        {
            $cells[] = $column->renderDataCell($model, $key, $index);
        }

        if ( $this->rowOptions instanceof Closure )
        {
            $options = call_user_func($this->rowOptions, $model, $key, $index, $this);
        }
        else
        {
            $options = $this->rowOptions;
        }
        $options['data-key'] = is_array($key) ? json_encode($key) : (string) $key;

        // Add "id" to all the rows
        $options['id'] = $this->options['id'] .'-row-'. $options['data-key'];

        return Html::tag('tr', implode('', $cells), $options);
    }


    /**
     * {@inheritdoc}
     */
    public function renderSummary()
    {
        // Add data attributes to <div class="summary">
        $this->summaryOptions['id'] = "{$this->options['id']}-summary";
        $this->summaryOptions['data-page'] = 1;
        $this->summaryOptions['data-count'] = $this->dataProvider->getCount();
        $this->summaryOptions['data-total'] = $this->summaryOptions['data-count'];

        // Pagination
        if ( ($pagination = $this->dataProvider->getPagination()) !== false )
        {
            $this->summaryOptions['data-total'] = $this->dataProvider->getTotalCount();
            $this->summaryOptions['data-page'] = $pagination->getPage() + 1;
        }

        return parent::renderSummary();
    }
}
