<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 *
 * @see https://github.com/PHPOffice/PhpSpreadsheet/tree/1.29.0
 * @see https://github.com/yidas/phpspreadsheet-helper
 * @see https://github.com/spatie/simple-excel
 * @see https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes
 */

namespace dezero\modules\sync\excel;

use Dz;
use dezero\helpers\StringHelper;
use Yii;

/**
 * Helper class to work with Excel files
 */
class ExcelHelper
{
    /**
     * Alpha to Number
     *
     * Optimizing from \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString()
     *
     * @example A => 1, AA => 27
     *
     * @param int $n Excel column alpha
     */
    public static function alpha2num(string $a) : int
    {
        $r = 0;
        $l = strlen($a);
        for ($i = 0; $i < $l; $i++)
        {
            $r += pow(26, $i) * (ord($a[$l - $i - 1]) - 0x40);
        }

        return (int)$r;
    }


    /**
     * Number to Alpha
     *
     * Optimizing from \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex()
     *
     * @example 1 => A, 27 => AA
     *
     * @param int $n column number
     */
    public static function num2alpha(int $n) : string
    {
        $n = $n - 1;
        $r = '';
        for ($i = 1; $n >= 0 && $i < 10; $i++)
        {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
            $n -= pow(26, $i);
        }

        return (string)$r;
    }


    /**
     * Normalize title
     *
     * @see PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    public static function normalizeTitle(string $title) : string
    {
        // Invalid characters
        $vec_invalid_characters = ['*', ':', '/', '\\', '?', '[', ']'];

        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
        $title = str_replace($vec_invalid_characters, '', $title);

        // Maximum 31 characters allowed for sheet title
        return StringHelper::substr($title, 0, 31);
    }
}
