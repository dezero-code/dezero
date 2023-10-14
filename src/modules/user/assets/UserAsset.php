<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\assets;

use yii\web\AssetBundle;

/**
 * Asset bundle for User module
 */
class UserAsset extends AssetBundle
{
    public $sourcePath = '@core/assets/js';

    public $css = [
    ];

    public $js = [
        'modules/user.js',
        'modules/userStatus.js',
    ];

}
