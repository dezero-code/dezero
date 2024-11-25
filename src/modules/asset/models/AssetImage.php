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
use dezero\base\Image;
use dezero\contracts\ConfigInterface;
use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\Url;
use dezero\modules\asset\components\ImageConfigurator;
use dezero\modules\asset\models\query\AssetFileQuery;
use dezero\modules\asset\models\AssetFile;
use Dz;
use user\models\User;
use yii\db\ActiveQueryInterface;
use Yii;

/**
 * AssetImage is a sublcass from AssetFile model class ("asset_file" database table)
 */
class AssetImage extends AssetFile implements ConfigInterface
{
    /**
     * @var \dezero\modules\asset\components\ImageConfigurator
     */
    private $configurator;


    /**
     * @var \dezero\base\Image
     */
    public $image;


    /**
     * @var array
     */
    private $vec_config;


    /**
     * @var string Asset Type
     */
    // public $asset_type = parent::ASSET_TYPE_IMAGE;



    /*
    |--------------------------------------------------------------------------
    | EVENTS
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    /*
    public function beforeValidate()
    {
        if ( $this->loadImage() )
        {
            $vec_dimensions = [
                'width'     => $this->image->getWidth(),
                'height'    => $this->image->getHeight()
            ];
            $this->file_options = Json::encode($vec_dimensions);
        }

        return parent::beforeValidate();
    }
    */



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
            $this->configurator = Dz::makeObject(ImageConfigurator::class, [$this->asset_type]);
        }

        return $this->configurator;
    }


    /**
     * Return special subdirectory where preset images are stored
     */
    public function getPresetsDirectory() : string
    {
        return $this->config ? $this->file_path . $this->config->getPresetsDirectory() : $this->file_path . '_';
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
    | FILE / IMAGE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * {@inheritdoc}
     */
    public function loadFile() : bool
    {
        $is_load_file = parent::loadFile();
        if ( $is_load_file )
        {
            $this->loadImage();
        }

        return $is_load_file;
    }


    /**
     * Load Image class object
     */
    public function loadImage() : bool
    {
        if ( $this->image === null )
        {
            $this->image = Image::load($this->getRelativePath());
        }

        return $this->image && $this->image->file && $this->image->file->exists();
    }


    /**
     * Delete an image file from filesystem
     */
    public function deleteFile() : bool
    {
        if ( ! $this->loadFile() )
        {
            return false;
        }

        // Delete all preset images
        $this->deleteAllPresets();

        // Finaly, delete original image
        return $this->file->delete();
    }


    /*
    |--------------------------------------------------------------------------
    | PRESETS
    |--------------------------------------------------------------------------
    */


    /**
     * Generate preset/thumbnail image given as input parameter
     */
    public function generatePreset(string $preset_name, ?string $destination_path = null) : bool
    {
        try
        {
            if ( ! $this->loadImage() )
            {
                return false;
            }

            // Destination path
            if ( $destination_path === null )
            {
                $destination_path = $this->getPresetsDirectory() . DIRECTORY_SEPARATOR;
            }

            return Yii::$app->imageManager->generatePreset($this->image, $destination_path, $this->getPreset($preset_name)) !== null;
        }
        catch (\Exception $e)
        {
            return false;
        }
    }


    /**
     * Generate several preset/thumbnail images given as input parameter
     */
    public function generatePresets(array $vec_presets, ?string $destination_path = null) : bool
    {
        if ( empty($vec_presets) )
        {
            return false;
        }

        $is_result = true;
        foreach ( $vec_presets as $preset_name )
        {
            if ( ! $this->generatePreset($preset_name, $destination_path) )
            {
                $is_result = false;
            }
        }

        return $is_result;
    }


    /**
     * Generate all preset/thumbnails images defined on the configuration file
     */
    public function generateAllPresets(?string $destination_path = null) : bool
    {
        $vec_presets = array_keys($this->getPresets());

        return $this->generatePresets($vec_presets, $destination_path);
    }


    /**
     * Return the Image object for a preset given by name
     */
    public function getPresetImage(string $preset_name) : ?Image
    {
        if ( ! $this->loadImage() )
        {
            return null;
        }

        $preset_file_name = Yii::$app->imageManager->getPresetFilename($this->image, $this->getPreset($preset_name));
        if ( $preset_file_name === null )
        {
            return null;
        }

        $preset_full_path = $this->getPresetsDirectory() . DIRECTORY_SEPARATOR . $preset_file_name;

        return Image::load($preset_full_path);
    }


    /**
     * Return all preset/thumbnails images defined on the configuration file
     */
    public function getAllPresets() : array
    {
        $vec_images = [];
        $vec_presets = array_keys($this->getPresets());
        foreach ( $vec_presets as $preset_name )
        {
            $vec_images[$preset_name] = $this->getPresetImage($preset_name);
        }

        return $vec_images;
    }


    /**
     * Delete all preset images objects
     */
    public function deleteAllPresets() : void
    {
        $vec_preset_images = $this->getAllPresets();
        if ( empty($vec_preset_images) )
        {
            return;
        }

        foreach ( $vec_preset_images as $preset_image )
        {
            if ( $preset_image && $preset_image->file )
            {
                $preset_image->file->delete();
            }
        }
    }


    /**
     * Return URL for an image
     */
    public function imageUrl(?string $preset_name = null, bool $is_generate_preset = true) : string
    {
        try
        {
            // Check URL for a preset?
            $preset_image = null;
            if ( $preset_name !== null )
            {
                $preset_image = $this->getPresetImage($preset_name);
            }

            // Generate preset image if not exists?
            if ( $preset_name !== null && $is_generate_preset && ( $preset_image === null || ! $preset_image->file || ! $preset_image->file->exists() ) )
            {
                $this->generatePreset($preset_name);
                $preset_image = $this->getPresetImage($preset_name);
            }

            // Return URL for ORIGINAL image file
            if ( $preset_image === null || ! $preset_image->file || ! $preset_image->file->exists() )
            {
                return parent::url();
            }

            // Return URL for a PRESET image file
            $url = str_replace(Yii::getAlias('@webroot'), '', $preset_image->file->filePath() );

            return Url::to($url);
        }

        // If any error, return URL for original image
        catch (\Exception $e)
        {
            return parent::url();
        }
    }
}
