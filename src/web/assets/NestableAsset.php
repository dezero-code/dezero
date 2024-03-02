<?php
/**
 * Asset class for Nestable plugin
 */

namespace dezero\web\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for nestable plugin
 */
class NestableAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'nestable/jquery.nestable.js',
        'nestable/dezero.nestable.js'
    ];
}
