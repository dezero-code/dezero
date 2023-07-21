<?php
/**
 * DateHelper class file for Dz Framework
 */

namespace dezero\helpers;

use Dz;
use Yii;

/**
 * Helper class for working with dates
 */
class DateHelper
{
    const DATE_FORMAT = 'php:Y-m-d';
    const DATETIME_FORMAT = 'php:Y-m-d H:i:s';
    const TIME_FORMAT = 'php:H:i:s';


    /**
     * Parses from UNIX timestamp format ot string "d/m/Y - H:i" date format
     */
    public static function toFormat(int $timestamp, string $format = 'd/m/Y - H:i') : string
    {
        return date($format, $timestamp);
    }
}
