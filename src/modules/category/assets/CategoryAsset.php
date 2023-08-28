<?php
/**
 * Asset class for Category module
 */

namespace dezero\modules\category\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle
 */
class CategoryAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'modules/category.js'
    ];

}
