<?php
/**
 * DateHelper class file for Dz Framework
 */

namespace dezero\helpers;

use DateTime;
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
     * Parses from UNIX timestamp format to string "d/m/Y - H:i" date format
     */
    public static function toFormat(int $timestamp, string $format = 'd/m/Y - H:i') : string
    {
        return date($format, $timestamp);
    }


    /**
     * Parses from formatted date to UNIX format
     *
     * Automatically detects these date formats:
     *   - d/m/Y
     *   - d/m/Y H:i
     *   - d/m/Y - H:i (or "d/m/Y-H:i")
     */
    public static function toUnix(string $date, ?string $format = null) : ?int
    {
        // If not format has been defined, try to figure out
        if ( $format === null )
        {
            $format = self::DATETIME_FORMAT;
            if ( preg_match("/\//", $date) )
            {
                $format = 'd/m/Y H:i';
                if ( preg_match("/\:/", $date) )
                {
                    $date = strtr($date, [
                        ' - '   => '',
                        '- '    => '',
                        ' -'    => '',
                        '-'     => ''
                    ]);
                }

                // Add an hour (12:00) if it has not be specified
                else
                {
                    $date .= ' 12:00';
                }
            }
        }

        $date = DateTime::createFromFormat($format, $date);
        if ( $date === false )
        {
            return null;
        }

        return $date->getTimestamp();
    }
}
