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
use dezero\modules\asset\components\ImageConfigurator;
use dezero\modules\asset\models\AssetImage;
use Dz;
use Spatie\Image\Manipulations;
use Yii;
use yii\base\Component;

/**
 * ImageManager - Helper classes collection for working with AssetImage models and Image objects
 */
class ImageManager extends Component
{
    /**
     * @var \dezero\modules\asset\components\ImageConfigurator
     */
    private $configurator;


    /**
     * @var array
     */
    private $vec_config;


    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION BUILDER
    |--------------------------------------------------------------------------
    */

    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : ImageConfigurator
    {
        if ( $this->configurator === null )
        {
            $this->configurator = Dz::makeObject(ImageConfigurator::class, ['image']);
        }

        return $this->configurator;
    }


    /**
     * Return special subdirectory where preset images are stored
     */
    public function getPresetsDirectory(Image $image) : string
    {
        return $this->config ? $image->file->dirname() . DIRECTORY_SEPARATOR . $this->config->getPresetsDirectory() : $image->file->dirname() . DIRECTORY_SEPARATOR . '_';
    }


    /**
     * Alias of getPresetsDirectory() method
     */
    public function presetsDirectory() : string
    {
        return $this->getPresetsDirectory();
    }


    /**
     * Return the configuration for the presets
     */
    public function getPresets() : array
    {
        return $this->config ? $this->config->getPresets() : [];
    }


    /**
     * Return the configuration for a preset name
     */
    public function getPreset($name) : array
    {
        return $this->config ? $this->config->getPreset($name) : [];
    }


    /*
    |--------------------------------------------------------------------------
    | IMAGE MANAGER METHODS
    |--------------------------------------------------------------------------
    */

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


    /**
     * Delete all preset images
     */
    public function deleteAllPresets(Image $image) : bool
    {
        // Ensure image exists
        if ( ! $image || ! $image->file || ! $image->file->exists() )
        {
            return false;
        }

        // Check preset directory exists
        $preset_full_path = $this->getPresetsDirectory($image);
        if ( ! file_exists($preset_full_path) )
        {
            return false;
        }

        $vec_preset_images = $this->getAllPresets($image);
        if ( empty($vec_preset_images) )
        {
            return false;
        }

        foreach ( $vec_preset_images as $preset_image )
        {
            if ( $preset_image && $preset_image->file )
            {
                $preset_image->file->delete();
            }
        }

        return true;
    }


    /**
     * Return all preset/thumbnails images defined on the configuration file
     */
    public function getAllPresets(Image $image) : array
    {
        $vec_images = [];
        $vec_presets = array_keys($this->getPresets());
        foreach ( $vec_presets as $preset_name )
        {
            $vec_images[$preset_name] = $this->getPresetImage($preset_name, $image);
        }

        return $vec_images;
    }


    /**
     * Return the Image object for a preset given by name
     */
    public function getPresetImage(string $preset_name, Image $image) : ?Image
    {
        $preset_file_name = $this->getPresetFilename($image, $this->getPreset($preset_name));
        if ( $preset_file_name === null )
        {
            return null;
        }

        $preset_full_path = $this->getPresetsDirectory($image) . DIRECTORY_SEPARATOR . $preset_file_name;

        return Image::load($preset_full_path);
    }

}
