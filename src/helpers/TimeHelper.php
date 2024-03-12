<?php
/**
 * TimeHelper class file for Dz Framework
 */

namespace dezero\helpers;

use DateTime;
use Dz;
use Yii;

/**
 * Helper class for working with time conversions
 */
class TimeHelper
{
    /**
     * @var int Number of seconds in a minute.
     */
    public const SECONDS_MINUTE = 60;


    /**
     * @var int Number of seconds in an hour.
     */
    public const SECONDS_HOUR = 3600;


    /**
     * @var int Number of seconds in a day.
     */
    public const SECONDS_DAY = 86400;


    /**
     * Converts seconds to hh:mm:ss or mm:ss format
     *
     * @see https://philfrilling.com/blog/2017-01/php-convert-seconds-hhmmss-format
     */
    public static function toFormat(int $seconds) : string
    {
        if ( $seconds <= 0 )
        {
            return '00:00';
        }

        $hours = floor($seconds / self::SECONDS_HOUR);
        $minutes = floor($seconds / 60 % 60);
        $seconds = $seconds % 60;

        if ( $hours > 0 )
        {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
