<?php
/**
 * Class ArrayHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\helpers;

use dezero\helpers\StringHelper;

class ArrayHelper extends \yii\helpers\ArrayHelper
{
    /**
     * Filters empty strings from an array.
     *
     * @param array $array
     * @return array
     */
    public static function filterEmptyStringsFromArray(array $array) : array
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
    public static function firstKey(array $array)
    {
        if ( function_exists( 'array_key_first' ) )
        {
            return array_key_first($array);
        }

        if ( empty($array) )
        {
            return null;
        }

        reset($array);

        return key($array);

        /** @noinspection LoopWhichDoesNotLoopInspection */
        /*
        foreach ( $array as $key => $value )
        {
            return $key;
        }

        return null;
        */
    }


    /**
     * Returns the first value in a given array.
     *
     * @param array $array
     * @return mixed The first value, or null if $array isn’t an array, or is empty.
     */
    public static function firstValue(array $array)
    {
        return !empty($array) ? reset($array) : null;
    }


    /**
     * Returns the last key in a given array.
     *
     * @param array $array
     * @return string|int|null The last key, whether that is a number (if the array is numerically indexed) or a string, or null if $array isn’t an array, or is empty.
     */
    public static function lastKey(array $array)
    {
        if ( function_exists( 'array_key_last' ) )
        {
            return array_key_last($array);
        }

        if ( empty($array) )
        {
            return null;
        }

        end($array);

        return key($array);

        /** @noinspection LoopWhichDoesNotLoopInspection */
        /*
        foreach ( $array as $key => $value )
        {
            return $key;
        }

        return null;
        */
    }


    /**
     * Returns the last value in a given array.
     *
     * @param array $array
     * @return mixed The last value, or null if $array isn’t an array, or is empty.
     */
    public static function lastValue(array $array)
    {
        return !empty($array) ? end($array) : null;
    }


    /**
     * Removes the passed characters from the left/right of all strings.
     */
    public static function trim(array $array) : array
    {
        foreach( $array as $key => $item )
        {
            if ( is_string( $item ) )
            {
                $array[$key] = StringHelper::trim($item);
            }
        }

        return $array;
    }
}
