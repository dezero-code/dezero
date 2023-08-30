<?php
/**
 * Asset class for Krajee Fileinput plugin
 */

namespace dezero\modules\asset\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle
 */
class FileinputAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'krajee-fileinput/dezero.fileinput.js'
    ];

}
