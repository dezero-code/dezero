<?php
/**
 * Asset class for this module
 */

namespace dezero\modules\user\assets;

use yii\web\AssetBundle;

/**
 * Main backend application asset bundle.
 */
class UserAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'modules/user.js',    ];

}
