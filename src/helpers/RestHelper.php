<?php
/**
 * RestHelper class file for Dezero Framework
 */

namespace dezero\helpers;

use dezero\db\Connection;
use Dz;
use Yii;

/**
 * Helper class to work with REST API
 */
class RestHelper
{
    /**
     * Convert an associative array to REST API format:
     *
     *  ['key' => 'value'] to ['name' => 'key', 'value' => 'value']
     */
    public static function array(array $vec_data) : array
    {
        if ( ! is_array($vec_data) || empty($vec_data) )
        {
            return $vec_data;
        }

        $vec_output = [];
        foreach ( $vec_data as $key => $value )
        {
            $vec_output[] = [
                'name'  => $key,
                'value' => is_array($value) ? self::array($vec_data) : $value
            ];
        }

        return $vec_output;
    }


    /**
     * Convert a date to REST API format: "Y-m-d H:i:s"
     */
    public static function date(int $unix_date) : string
    {
        return date("Y-m-d H:i:s", $unix_date);
    }
}
