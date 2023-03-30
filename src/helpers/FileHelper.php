<?php
/**
 * Class FileHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\helpers;

use dezero\helpers\Log;
use Throwable;
use UnexpectedValueException;
use yii\base\ErrorException;
use yii\base\InvalidArgumentException;
use Yii;

class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * Return the real file path from an alias or normalize it
     */
    public static function realPath(string $path) : string
    {
        if ( preg_match("/^\@/", $path) )
        {
            return Yii::getAlias($path);
        }

        return self::normalizePath($path);
    }


    /**
     * {@inheritdoc}
     */
    public static function createDirectory($path, $mode = 0775, $recursive = true)
    {
        // Accept alias pathes
        $path = self::realPath($path);

        return parent::createDirectory($path, $mode, $recursive);
    }


    /**
     * Check if a file is an image
     */
    public static function isImage(string $image_path) : bool
    {
        if ( !is_file($image_path) )
        {
            return false;
        }

        // Disable error reporting, to prevent PHP warnings
        $error_reporting = error_reporting(0);

        // Fetch the image size and mime type
        $vec_image_info = getimagesize($image_path);

        // Turn on error reporting again
        error_reporting($error_reporting);

        // Make sure that the image is readable and valid
        if ( ! is_array($vec_image_info) || count($vec_image_info) < 3)
        {
            return false;
        }

        return true;
    }


    /**
     * {@inheritdoc}
     */
    public static function unlink($path): bool
    {
        // BaseFileHelper::unlink() doesn't seem to catch all possible exceptions
        try
        {
            return parent::unlink($path);
        }
        catch (Throwable)
        {
            return false;
        }
    }


    /**
     * Removes all of a directory’s contents recursively.
     *
     * @param string $dir the directory to be deleted recursively.
     * @param array $options options for directory remove. Valid options are:
     * - `traverseSymlinks`: bool, whether symlinks to the directories should be traversed too.
     *   Defaults to `false`, meaning the content of the symlinked directory would not be deleted.
     *   Only symlink would be removed in that default case.
     * - `filter`: callback (see [[findFiles()]])
     * - `except`: array (see [[findFiles()]])
     * - `only`: array (see [[findFiles()]])
     * @throws InvalidArgumentException if the dir is invalid
     * @throws ErrorException in case of failure
     */
    public static function clearDirectory(string $dir, array $options = []) : void
    {
        if ( ! is_dir($dir) )
        {
            throw new InvalidArgumentException("The dir argument must be a directory: $dir");
        }

        // Adapted from [[removeDirectory()]], plus addition of filters, and minus the root directory removal at the end
        if ( ! ( $handle = opendir($dir) ) )
        {
            return;
        }

        if ( ! isset($options['basePath']) )
        {
            $options['basePath'] = realpath($dir);
            $options = static::normalizeOptions($options);
        }

        while ( ( $file = readdir($handle) ) !== false )
        {
            if ( $file === '.' || $file === '..' )
            {
                continue;
            }
            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if ( static::filterPath($path, $options) )
            {
                if ( is_dir($path) )
                {
                    try
                    {
                        static::removeDirectory($path, $options);
                    }
                    catch ( UnexpectedValueException $e )
                    {
                        // Ignore if the folder has already been removed.
                        if ( strpos($e->getMessage(), 'No such file or directory') === false )
                        {
                            Log::warning("Tried to remove " . $path . ", but it doesn't exist.");
                            throw $e;
                        }
                    }
                }
                else
                {
                    static::unlink($path);
                }
            }
        }
        closedir($handle);
    }


    /**
     * Returns whether a given directory is empty (has no files) recursively.
     *
     * @param string $dir the directory to be checked
     * @return bool whether the directory is empty
     * @throws InvalidArgumentException if the dir is invalid
     * @throws ErrorException in case of failure
     */
    public static function isEmptyDirectory(string $dir) : bool
    {
        if ( ! is_dir($dir) )
        {
            throw new InvalidArgumentException("The dir argument must be a directory: $dir");
        }

        if ( ! ($handle = opendir($dir) ) )
        {
            throw new ErrorException("Unable to open the directory: $dir");
        }

        // It's empty until we find a file
        $empty = true;

        while ( ( $file = readdir($handle) ) !== false )
        {
            if ( $file === '.' || $file === '..' )
            {
                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $file;
            if ( is_file($path) || ! static::isEmptyDirectory($path) )
            {
                $empty = false;
                break;
            }
        }

        closedir($handle);

        return $empty;
    }
}
