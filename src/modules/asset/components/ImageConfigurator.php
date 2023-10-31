<?php
/**
 * ImageConfigurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\components;

use dezero\base\Configurator;
use dezero\contracts\ConfiguratorInterface;
use Yii;

/**
 * Base class to handle configuration options for AssetImage models
 */
class ImageConfigurator extends Configurator implements ConfiguratorInterface
{
    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/images');
        if ( $vec_config === null )
        {
            return [];
        }

        $this->vec_config = $vec_config;
        return $this->vec_config;
    }


    /**
     * Return the default configuration for the category type
     */
    public function defaultConfiguration() : array
    {
        // Try with default configuration defined on "/app/config/images"
        $vec_config = Yii::$app->config->get('components/images');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Default UNIX file permissions
            // --> IMPORTANT: It must be in NUMERIC format starting WITHOUT 0
            'default_permissions' => 755,

            /**
             * Image optimizer tools
             *
             * @todo Used in dezero\base\Image::loadOptimizers();
             *
             * @see https://github.com/spatie/image-optimizer
             * @see https://spatie.be/docs/image/v1/image-manipulations/optimizing-images
             */
            'optimizers' => [
                'Jpegoptim' => [
                    'quality'       => 85,
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Jpegoptim']
                ],
                'Pngquant' => [
                    'quality'       => 85,
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Pngquant']
                ],
                'Optipng' => [
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Optipng']
                ],
                'Svgo' => [
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Svgo']
                ],
                'Gifsicle' => [
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Gifsicle']
                ],
                'Cwebp' => [
                    'quality'       => 80,
                    'binaryPath'    => null,    // Yii::$app->params['optimizers']['Cwebp']
                ],
            ],

            // Presets or thumbnails
            'presets_dir'  => '_',
            'presets' => [
                'large' => [
                    'name'          => 'BASE size - 700x700 (prefijo "B_")',
                    'prefix'        => 'B_',
                    'width'         => 700,
                    'height'        => 700,
                    'is_upscale'    => false
                ],
                'medium'=> [
                    'name'          => 'SMALL size - 300x300 (prefijo "S_")',
                    'prefix'        => 'S_',
                    'width'         => 300,
                    'height'        => 300,
                    'is_upscale'    => false
                ],
                'small' => [
                    'name'          => 'THUMBNAIL size - 140x140 (prefijo "T_")',
                    'prefix'        => 'T_',
                    'width'         => 140,
                    'height'        => 140,
                    'is_upscale'    => false
                ],
            ]
        ];
    }


    /**
     * Return optimizers configuration
     */
    public function getOptimizers() : array
    {
        return $this->get('optimizers');
    }


    /**
     * Return special subdirectory where preset images are stored
     */
    public function getPresetsDirectory() : string
    {
        return $this->get('presets_dir');
    }


    /**
     * Return presets configuration
     */
    public function getPresets() : array
    {
        return $this->get('presets');
    }


    /**
     * Return a specific preset configuration
     */
    public function getPreset(string $name) : array
    {
        return $this->get('presets', $name);
    }
}
