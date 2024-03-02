<?php
/**
 * Asset class for Export to Excel buttons
 */

namespace dezero\modules\sync\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for export to excel
 */
class ExportExcelAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'sync/dezero.export.js'
    ];
}
