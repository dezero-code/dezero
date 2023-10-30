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
use dezero\helpers\ArrayHelper;
use dezero\helpers\Json;
use dezero\helpers\Url;
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
    | CONFIGURATION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Load configuration options
     */
    public function loadConfig() : void
    {
        if ( empty($this->vec_config) )
        {
            $this->vec_config = Yii::$app->config->get('images');
        }
    }


    /**
     * Return a preset configuration
     */
    public function getPresetConfig(string $preset_name) : ?array
    {
        $this->loadConfig();
        return $this->vec_config['presets'][$preset_name] ?? null;
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
     * Return the Image object for a preset given by name
     */
    public function getPresetImage(string $preset_name) : ?Image
    {
        $preset_file_name = $this->getPresetFileName($preset_name);
        if ( $preset_file_name === null )
        {
            return null;
        }

        $preset_full_path = $this->getPresetsPath() . $preset_file_name;

        return Image::load($preset_full_path);
    }


    /**
     * Return image file name for a preset
     */
    public function getPresetFileName(string $preset_name) : ?string
    {
        $vec_preset_config = $this->getPresetConfig($preset_name);
        if ( empty($vec_preset_config) || !isset($vec_preset_config['name']) )
        {
            return null;
        }

        return isset($vec_preset_config['prefix']) ? $vec_preset_config['prefix'] . $this->file_name : $this->file_name;
    }


    /**
     * Return directory where preset images are stored
     */
    public function getPresetsPath() : string
    {
        $this->loadConfig();
        $presets_path = $this->file_path;
        if ( isset($this->vec_config['preset_dir']) )
        {
            $presets_path .= $this->vec_config['preset_dir'] . DIRECTORY_SEPARATOR;
        }

        return $presets_path;
    }


    /**
     * Return all preset/thumbnails images defined on the configuration file
     */
    public function getAllPresets() : array
    {
        $this->loadConfig();
        if ( ! isset($this->vec_config['presets']) || empty($this->vec_config['presets']) )
        {
            return [];
        }

        $vec_images = [];
        $vec_presets = array_keys($this->vec_config['presets']);
        foreach ( $vec_presets as $preset_name )
        {
            $vec_images[$preset_name] = $this->getPresetImage($preset_name);
        }

        return $vec_images;
    }


    /**
     * Generate preset/thumbnail image given as input parameter
     */
    public function generatePreset(string $preset_name, ?string $destination_path = null) : bool
    {
        return $this->generatePresets([$preset_name], $destination_path);
    }


    /**
     * Generate several preset/thumbnail images given as input parameter
     */
    public function generatePresets(array $vec_presets, ?string $destination_path = null) : bool
    {
        if ( ! $this->loadImage() )
        {
            return false;
        }

        // Destination path
        if ( $destination_path === null )
        {
            $destination_path = $this->getPresetsPath();
        }

        // Ensure directory exists & generate preset
        $destination_directory = File::ensureDirectory($destination_path);
        if ( $destination_directory && $destination_directory->exists() )
        {
            foreach ( $vec_presets as $preset_name )
            {
                // Check preset exists
                $vec_preset_config = $this->getPresetConfig($preset_name);
                if ( ! empty($vec_preset_config) &&  isset($vec_preset_config['width']) && isset($vec_preset_config['height']) )
                {
                    $preset_file_name = isset($vec_preset_config['prefix']) ? $vec_preset_config['prefix'] . $this->file_name : $this->file_name;
                    $this->image
                        ->resizeMax($vec_preset_config['width'] ,$vec_preset_config['height'])
                        ->optimize()
                        ->save($destination_path . $preset_file_name);
                }
            }
        }

        return true;
    }


    /**
     * Generate all preset/thumbnails images defined on the configuration file
     */
    public function generateAllPresets(?string $destination_path = null) : bool
    {
        $this->loadConfig();
        if ( isset($this->vec_config['presets']) && !empty($this->vec_config['presets']) )
        {
            $vec_presets = array_keys($this->vec_config['presets']);

            return $this->generatePresets($vec_presets, $destination_path);
        }

        return false;
    }


    /**
     * Delete all preset images objects
     */
    public function deleteAllPresets() : void
    {
        $vec_preset_images = $this->getAllPresets();
        if ( !empty($vec_preset_images) )
        {
            foreach ( $vec_preset_images as $preset_image )
            {
                if ( $preset_image && $preset_image->file )
                {
                    $preset_image->file->delete();
                }
            }
        }
    }


    /**
     * Return URL for an image
     */
    public function imageUrl(?string $preset_name = null) : string
    {
        // Check URL for a preset?
        $preset_image = null;
        if ( $preset_name !== null )
        {
            $preset_image = $this->getPresetImage($preset_name);
        }

        // Return URL for ORIGINAL image file
        if ( $preset_image === null || ! $preset_image->file )
        {
            return parent::url();
        }

        // Return URL for a PRESET image file
        $url = str_replace(Yii::getAlias('@webroot'), '', $preset_image->file->filePath() );

        return Url::to($url);
    }
}
