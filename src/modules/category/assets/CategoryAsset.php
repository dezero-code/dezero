<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for Category module
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
