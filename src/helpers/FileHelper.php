<?php
/**
 * Class FileHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\helpers;

class FileHelper extends \yii\helpers\FileHelper
{
    /**
     * @inheritdoc
     */
    public static function normalizePath($path, $ds = DIRECTORY_SEPARATOR): string
    {
        // dd($path);
        // Normalize the path
        $path = parent::normalizePath($path, $ds);

        return $path;
    }
}

