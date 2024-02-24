<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\system\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for System module
 */
class SystemAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    // public $css = [
    //     'modules/system.css'
    // ];

    public $js = [
        'modules/system.js'
    ];
}
