<?php
/**
 * Asset class for Jasny Fileinput plugin
 */

namespace dezero\modules\asset\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for custom Jasny FileInput
 */
class JasnyFileinputAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
        'jasny-fileinput/css/jasny-bootstrap.min.css'
    ];

    public $js = [
        // 'jasny-fileinput/dezero.fileInput.js'
        'jasny-fileinput/js/jasny-bootstrap.min.js'
    ];

}
