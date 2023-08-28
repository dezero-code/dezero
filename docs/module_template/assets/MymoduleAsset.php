<?php
/**
 * Asset class for Mymodule module
 */

namespace dezero\modules\mymodule\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle
 */
class MymoduleAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'modules/mymodule.js'
    ];

}
