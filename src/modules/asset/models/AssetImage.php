<?php
/**
 * AssetImage model class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\models;

use dezero\base\File;
use dezero\helpers\ArrayHelper;
use dezero\modules\asset\models\query\AssetFileQuery;
use dezero\modules\asset\models\AssetFile;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * AssetImage is a sublcass from AssetFile model class ("asset_file" database table)
 */
class AssetImage extends AssetFile
{
    /**
     * @var string Asset Type
     */
    public $asset_type = parent::ASSET_TYPE_IMAGE;
}
