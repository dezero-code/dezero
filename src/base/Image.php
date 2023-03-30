<?php
/**
 * Base Image class file based on "spatie/image" library
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\base;

use dezero\base\File;
use dezero\helpers\FileHelper;
use Spatie\Image\Image as SpatieImage;
use Spatie\Image\Manipulations;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Cwebp;
use Spatie\ImageOptimizer\Optimizers\Gifsicle;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;
use Spatie\ImageOptimizer\Optimizers\Optipng;
use Spatie\ImageOptimizer\Optimizers\Pngquant;
use Spatie\ImageOptimizer\Optimizers\Svgo;
use Yii;


/**
 * File is the base class to handle images
 *
 * @see https://github.com/spatie/image
 * @see https://spatie.be/docs/image/v1/introduction
 */
class Image extends SpatieImage
{
    /**
     * @var File object
     */
    public File $file;


    /**
     * File constructor
     */
    public function __construct(string $pathToImage)
    {
        // Load File object and get real path
        $this->file = File::load($pathToImage);

        parent::__construct($pathToImage);
    }


    /**
     * {@inheritdoc}
     */
    public static function load(string $pathToImage): static
    {
        // Return the real file path from a Yii alias or normalize it
        $pathToImage = FileHelper::realPath($pathToImage);

        return new static($pathToImage);
    }


    /**
     * {@inheritdoc}
     */
    public function save(string $outputPath = ''): void
    {
        // Accept Yii alias pathes and relative pathes
        if ($outputPath !== '')
        {
            $outputPath = $this->file->resolveDestinationPath($outputPath);
        }

        // Load default Optimizers
        $this->optimizerChain = $this->optimizerChain ?? self::loadOptimizers();

        parent::save($outputPath);
    }


    /*
    |--------------------------------------------------------------------------
    | RESIZE METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Resize image process. Can use several fit methods (CONTAIN by default)
     *
     * Resizes the image to fit within the width and height boundaries
     * without cropping, distorting or altering the aspect ratio.
     *
     * @see \Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resize(int $width, int $height, string $fitMethod = Manipulations::FIT_CONTAIN) : static
    {
        $this->fit($fitMethod, $width, $height);
        return $this;
    }


    /**
     * Resize image using FILL fit method
     *
     * Resizes the image to fit within the width and height boundaries without
     * cropping or distorting the image, and the remaining space is filled with the
     * background color. The resulting image will match the constraining dimensions.
     *
     * @see \Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resizeFill(int $width, int $height) : static
    {
        return $this->resize($width, $height, Manipulations::FIT_FILL);
    }


    /**
     * Resize image using MAX fit method
     *
     * Resizes the image to fit within the width and height boundaries without
     * cropping, distorting or altering the aspect ratio, and will also not increase
     * the size of the image if it is smaller than the output size.
     *
     * @see \Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resizeMax(int $width, int $height) : static
    {
        return $this->resize($width, $height, Manipulations::FIT_MAX);
    }


    /**
     * Resize image forcing width and height (STRETCH fit method)
     *
     * Stretches the image to fit the constraining dimensions exactly.
     * The resulting image will fill the dimensions, and will not maintain
     * the aspect ratio of the input image.
     *
     * @see \Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resizeForce(int $width, int $height) : static
    {
        return $this->resize($width, $height, Manipulations::FIT_STRETCH);
    }


    /**
     * Resize image using CROP fit method
     *
     * Resizes the image to fill the width and height boundaries and crops
     * any excess image data. The resulting image will match the width and
     * height constraints without distorting the image.
     *
     * @see \Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resizeCrop(int $width, int $height) : static
    {
        return $this->resize($width, $height, Manipulations::FIT_CROP);
    }


    /**
     * Resize image forcing width and height (FILL MAX fit method)
     *
     * Resizes the image to fit within the width and height boundaries without
     * cropping but upscaling the image if it’s smaller. The finished image will
     * have remaining space on either width or height (except if the aspect ratio
     * of the new image is the same as the old image). The remaining space will
     * be filled with the background color. The resulting image will match
     * the constraining dimensions.
     *
     * @see Spatie\Image\Manipulations::fit()
     * @see https://spatie.be/docs/image/v1/image-manipulations/resizing-images
     */
    public function resizeFillMax(int $width, int $height) : static
    {
        return $this->resize($width, $height, Manipulations::FIT_FILL_MAX);
    }



    /*
    |--------------------------------------------------------------------------
    | OPTIMIZERS
    |--------------------------------------------------------------------------
    */

    /**
     * Load default optimizers
     *
     * Override from Spatie\ImageOptimizer\OptimizerChainFactory
     */
    public static function loadOptimizers(array $config = []): OptimizerChain
    {
        $jpegQuality = '--max=85';
        $pngQuality = '--quality=85';
        $webpQuality = '-q 80';

        if ( isset($config['quality']) )
        {
            $jpegQuality = '--max='.$config['quality'];
            $pngQuality = '--quality='.$config['quality'];
            $webpQuality = '-q '.$config['quality'];
        }

        $optimizer = new OptimizerChain();
        $optimizer->setTimeout(10);
        $optimizer->addOptimizer(new Jpegoptim([
            $jpegQuality,
            '--strip-all',
            '--all-progressive',
        ]));
        /*
        $optimizer->addOptimizer((new Jpegoptim([
            $jpegQuality,
            '--strip-all',
            '--all-progressive',
        // ]));
        ]))->setBinaryPath('/home/user/bin'));
        */

        $optimizer->addOptimizer(new Pngquant([
            $pngQuality,
            '--force',
            '--skip-if-larger',
        ]));

        $optimizer->addOptimizer(new Optipng([
            '-i0',
            '-o2',
            '-quiet',
        ]));

        $optimizer->addOptimizer(new Svgo([
            '--config=svgo.config.js',
        ]));

        $optimizer->addOptimizer(new Gifsicle([
            '-b',
            '-O3',
        ]));

        $optimizer->addOptimizer(new Cwebp([
            $webpQuality,
            '-m 6',
            '-pass 10',
            '-mt',
        ]));

        return $optimizer;
    }


    /*
    |--------------------------------------------------------------------------
    | PORT FROM FILE CLASS
    |--------------------------------------------------------------------------
    */


    /**
     * Return real path for the current filesystem object
     */
    public function realPath() : ?string
    {
        return $this->file->realPath();
    }


    /**
     * Return the size of current filesystem object in bytes
     */
    public function size(array $vec_options = []) : int
    {
        return $this->file->size($vec_options);
    }


    /**
     * Return the size of current filesystem object in the given unit
     */
    public function formatSize(?int $value = null) : string
    {
        return $this->file->formatSize($value);
    }


    /**
     * Deletes the current image
     */
    public function delete() : bool
    {
        return $this->file->delete();
    }

    /**
     * Returns the MIME type of the current file
     */
    public function mime() : ?string
    {
        return $this->file->mime();
    }
}
