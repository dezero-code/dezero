<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

use dezero\helpers\Html;
use dezero\grid\GridViewAsset;

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
     * @inheritdoc
     */
    public function init()
    {
        Html::addCssClass($this->options, 'dz-grid-view');

        parent::init();
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
     * @inheritdoc
     */
    public function renderItems()
    {
        $id = $this->options['id'];
        $this->tableOptions['id'] = $id .'-table';
        return
            '<div class="dz-loader-overlay"><div class="dz-loader loader loader-circle"></div></div>' .
            '<div class="table-responsive">' . parent::renderItems() .'</div>';
    }
}
