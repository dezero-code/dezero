<?php
/**
 * Class FileHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\helpers;

use Throwable;
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
}
