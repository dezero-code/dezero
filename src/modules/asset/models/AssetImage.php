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
            $destination_path = $this->file_path;
            if ( isset($this->vec_config['preset_dir']) )
            {
                $destination_path .= $this->vec_config['preset_dir'] . DIRECTORY_SEPARATOR;
            }
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
                    $this->image->resize($vec_preset_config['width'] ,$vec_preset_config['height'])->save($destination_path . $preset_file_name);
                }
            }
        }

        return true;
    }


    /**
     * Generate all preset/thumbnauls images defined on the configuration file
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
}
