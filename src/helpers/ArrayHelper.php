<?php
/**
 * Class ArrayHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\helpers;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Filters empty strings from an array.
     *
     * @param array $array
     * @return array
     */
    public static function filterEmptyStringsFromArray(array $array): array
    {
        return array_filter($array, function($value): bool {
            return $value !== '';
        });
    }


    /**
     * Returns the first key in a given array.
     *
     * @param array $array
     * @return string|int|null The first key, whether that is a number (if the array is numerically indexed) or a string, or null if $array isn’t an array, or is empty.
     */
    public static function firstKey(array $array): int|string|null
    {
        /** @noinspection LoopWhichDoesNotLoopInspection */
        foreach ( $array as $key => $value )
        {
            return $key;
        }

        return null;
    }


    /**
     * Returns the first value in a given array.
     *
     * @param array $array
     * @return mixed The first value, or null if $array isn’t an array, or is empty.
     */
    public static function firstValue(array $array): mixed
    {
        return !empty($array) ? reset($array) : null;
    }
}
