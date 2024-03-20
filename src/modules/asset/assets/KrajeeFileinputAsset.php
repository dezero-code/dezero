<?php
/**
 * Asset class for Krajee Fileinput plugin
 */

namespace dezero\modules\asset\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for custom Krajee FileInput
 */
class KrajeeFileinputAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'krajee-fileinput/dezero.fileInput.js'
    ];
}
