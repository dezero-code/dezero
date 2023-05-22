<?php
/**
 * Number class file
 */

namespace dezero\helpers;

use Yii;

/**
 * Helper class for working with numbers
 */
class Number
{
    /**
     * Three elements may be specified: "decimals", "decimalSeparator" and
     * "thousandSeparator". They correspond to the number of digits after
     * the decimal point, the character displayed as the decimal point,
     * and the thousands separator character.
     * new: override default value: 2 decimals, a comma (,) before the decimals
     * and no separator between groups of thousands
     */
    public static function default_format() : array
    {
        return [
            'decimals'          => 2,
            'decimalSeparator'  => ',',
            'thousandSeparator' => ''
        ];
    }


    /**
     * Formats the value as a number using PHP number_format() function.
     *
     * @param mixed $value
     * @param array $vec_number_format
     */
    public static function format($value, array $vec_number_format = []) : ?string
    {
        if ( $value === null || $value === '' )
        {
            return null;
        }

        // Format configuration
        $vec_default_format = self::default_format();
        $vec_format = [
            'decimals'          => isset($vec_number_format['decimals']) ? $vec_number_format['decimals'] : $vec_default_format['decimals'],
            'decimalSeparator'  => isset($vec_number_format['decimalSeparator']) ? $vec_number_format['decimalSeparator'] : $vec_default_format['decimalSeparator'],
            'thousandSeparator' => isset($vec_number_format['thousandSeparator']) ? $vec_number_format['thousandSeparator'] : $vec_default_format['thousandSeparator'],
        ];
        if ( is_numeric($value) )
        {
            return number_format($value, $vec_format['decimals'], $vec_format['decimalSeparator'], $vec_format['thousandSeparator']);
        }

        return $value;
    }


    /**
     * Turns the given formatted number (string) into a float
     *
     * @param $mixed $formatted_number
     */
    public static function unformat($formatted_number) : ?float
    {
        if ( $formatted_number === null || $formatted_number === '' )
        {
            return null;
        }

        // Only 'unformat' if parameter is not float already
        if ( is_float($formatted_number) )
        {
            return $formatted_number;
        }

        // Make the transformation
        $vec_default_format = self::default_format();
        $value = str_replace($vec_default_format['thousandSeparator'], '', $formatted_number);
        $value = str_replace($vec_default_format['decimalSeparator'], '.', $value);
        return (float) $value;
    }


    /**
     * Round a number to the nearest nth
     *
     * @param   int|float  number to round
     * @param   int  number to round to
     * @return  int
     */
    public function round($number, int $nearest = 5) : float
    {
        return round($number / $nearest) * $nearest;
    }
}
