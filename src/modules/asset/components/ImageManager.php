<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\components;

use dezero\base\File;
use dezero\base\Image;
use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use dezero\modules\asset\models\AssetImage;
use Spatie\Image\Manipulations;
use Yii;
use yii\base\Component;

/**
 * ImageManager - Helper classes collection for working with AssetImage models and Image objects
 */
class ImageManager extends Component
{
    /**
     * Generate preset/thumbnail image given as input parameter
     */
    public function generatePreset(Image $image, string $destination_path, array $vec_config) : ?Image
    {
        if ( empty($destination_path) )
        {
            return null;
        }

        // Ensure directory exists & generate preset
        $destination_directory = File::ensureDirectory($destination_path);
        if ( ! $destination_directory || ! $destination_directory->exists() )
        {
            return null;
        }

        // Width and/or height are required
        if ( ! isset($vec_config['width']) || !isset($vec_config['height']) )
        {
            return null;
        }

        // Generate preset image
        $preset_image_path = $destination_path . DIRECTORY_SEPARATOR . $this->getPresetFilename($image, $vec_config);

        // Resize method?
        $resize_method = $vec_config['resize_method'] ?? Manipulations::FIT_MAX;
        $image = $image->resize($vec_config['width'], $vec_config['height'], $resize_method);

        // Optimize enabled?
        if ( isset($vec_config['is_optimize']) && $vec_config['is_optimize'] )
        {
            $image = $image->optimize();
        }

        // Finally, save the preset image
        $image->save($preset_image_path);

        // \DzLog::dev($preset_image_path);


        return Image::load($preset_image_path);
    }


    /**
     * Return file name for a preset
     */
    public function getPresetFilename(Image $image, array $vec_config) : string
    {
        $file_prefix = $vec_config['prefix'] ?? '';

        return $file_prefix . $image->file->basename();
    }
}
