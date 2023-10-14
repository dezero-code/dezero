<?php
/**
 * Asset class for Asset module
 */

namespace dezero\modules\asset\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for files and images
 */
class AssetAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'modules/asset.js'
    ];

}
