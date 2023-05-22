<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\grid;

/**
 * This asset bundle provides the javascript files for the [[GridView]] widget.
 */
class GridViewAsset extends \yii\web\AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $js = [
        'grid/dezero.gridview.js',
    ];
}
