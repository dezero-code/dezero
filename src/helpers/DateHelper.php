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
     * Check if a date is valid in the given format
     */
    public static function isValid(string $date, string $format = 'd/m/Y') : bool
    {
        $date_time = DateTime::createFromFormat($format, $date);
        return $date_time && $date_time->format($format) === $date;

        // $validator = new \yii\validators\DateValidator(['format' => $format]);
        // return $validator->validate($date);
    }


    /**
     * Parses from UNIX timestamp format or DATE TIME format (Y-m-d H:i:s)
     * to string "d/m/Y - H:i" date format
     */
    public static function toFormat(int|string $timestamp, string $format = 'd/m/Y - H:i') : string
    {
        // Parses from UNIX timestamp format to string "d/m/Y - H:i" date format
        if ( is_numeric($timestamp) )
        {
            return date($format, $timestamp);
        }

        // Check if the date is in the correct format: Y-m-d
        if ( ! preg_match("/\d{4}-\d{2}-\d{2}/", $timestamp) )
        {
            return '';
        }

        // Parses from DATE TIME format (Y-m-d H:i:s) to string "d/m/Y - H:i" date format
        $unix_timestamp = self::toUnix($timestamp);
        return date($format, $unix_timestamp);
    }


    /**
     * Return current date in the given format
     */
    public static function now(string $format = 'd/m/Y - H:i') : string
    {
        return date($format);
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

        $date_time = DateTime::createFromFormat($format, $date);
        if ( $date_time === false )
        {
            return null;
        }

        return $date_time->getTimestamp();
    }
}
