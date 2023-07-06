<?php
/**
 * StringHelper class file for Dz Framework
 */

namespace dezero\helpers;

use Dz;
use dezero\helpers\ArrayHelper;
use Stringy\Stringy as BaseStringy;
use Yii;
use yii\helpers\HtmlPurifier;

/**
 * Helper class for working with strings
 */
class StringHelper extends \yii\helpers\StringHelper
{
    const UUID_PATTERN = '[A-Za-z0-9]{8}-[A-Za-z0-9]{4}-4[A-Za-z0-9]{3}-[89abAB][A-Za-z0-9]{3}-[A-Za-z0-9]{12}';


    /**
     * Create machine readable name
     */
    public static function readableName(string $text) : string
    {
        $text = strtr($text, [
            'á' => 'a',
            'Á' => 'a',
            'é' => 'e',
            'É' => 'e',
            'í' => 'i',
            'Í' => 'i',
            'ó' => 'o',
            'Ó' => 'o',
            'ú' => 'u',
            'Ú' => 'u',
            'ñ' => 'n',
            'Ñ' => 'n',
        ]);
        $text = self::strtolower($text);
        return preg_replace('@[^a-z0-9_]+@', '-', $text);
    }


    /**
     * Convert a string to UPPERCASE but having SPANISH special characters into account
     */
    public static function strtoupper(string $text, bool $is_use_mbstring = false) : string
    {
        if ( $is_use_mbstring )
        {
            $text = mb_strtoupper($text);
        }
        else
        {
            // Use C-locale for ASCII-only uppercase.
            $text = strtoupper($text);

            // Case flip Latin-1 accented letters
            $text = preg_replace_callback('/\\xC3[\\xA0-\\xB6\\xB8-\\xBE]/', '\dezero\helpers\StringHelper::unicodeCaseflip', $text);
        }

        return $text;
    }


    /**
     * Convert a string to LOWERCASE but having SPANISH special characters into account
     */
    public static function strtolower(string $text, bool $is_use_mbstring = false) : string
    {
        if ( $is_use_mbstring )
        {
            $text = mb_strtolower($text);
        }
        else
        {
            // Use C-locale for ASCII-only lowercase.
            $text = strtolower($text);

            // Case flip Latin-1 accented letters.
            $text = preg_replace_callback('/\\xC3[\\x80-\\x96\\x98-\\x9E]/', '\dezero\helpers\StringHelper::unicodeCaseflip', $text);
        }
        return $text;
    }


    /**
     * Alias of StringHelper::strtoupper() method
     *
     * Convert a string to UPPERCASE but having SPANISH special characters into account
     */
    public static function uppercase(string $text, bool $is_use_mbstring = false) : string
    {
        return self::strtoupper($text, $is_use_mbstring);
    }


    /**
     * Alias of StringHelper::strtolower() method
     *
     * Convert a string to LOWERCASE but having SPANISH special characters into account
     */
    public static function lowercase(string $text, bool $is_use_mbstring = false) : string
    {
        return self::strtolower($text, $is_use_mbstring);
    }


    /**
     * Compares UTF-8-encoded strings in a binary safe case-insensitive manner.
     *
     * @return int Returns < 0 if $str1 is less than $str2; > 0 if $str1 is greater than $str2, and 0 if they are equal.
     */
    public static function strcasecmp(string $str1, string $str2) : int
    {
        return strcmp(self::strtoupper($str1), self::strtoupper($str2));
    }


    /**
     * Alias of StringHelper::strcasecmp() method
     *
     * Compares UTF-8-encoded strings in a binary safe case-insensitive manner.
     */
    public static function compare(string $str1, string $str2) : int
    {
        return self::strcasecmp($str1, $str2);
    }


    /**
     * Remove white spaces
     *
     * Second version in https://pageconfig.com/post/remove-undesired-characters-with-trim_all-php
     */
    public static function trimAll(string $text) : string
    {
        return preg_replace('~\s*(<([^>]*)>[^<]*</\2>|<[^>]*>)\s*~', '$1', $text);
    }


    /**
     * Special trim with UTF-8 characters
     *
     * @see https://stackoverflow.com/questions/12837682/non-breaking-utf-8-0xc2a0-space-and-preg-replace-strange-behaviour
     */
    public static function trim(string $text) : string
    {
        $text = str_replace("\xc2\xa0", " ", $text);
        return trim($text);
    }


    /**
     * Clean all HTML tags of a string
     */
    public static function cleanHtml(string $text, ?array $vec_allowed_tags = [])
    {
        return HtmlPurifier::process($text, ['HTML.AllowedElements' => $vec_allowed_tags]);
    }


    /**
     * Clean text
     *
     * WARNING: It removes characteres like ¿ or accents(á,é,í,...)
     *
     * @see https://alvinalexander.com/php/how-to-remove-non-printable-characters-in-string-regex
     */
    public static function cleanText(string $text) : string
    {
        $text = self::removeInvisibleCharacters($text);

        // Remove all CONTROL characters
        if ( ! Dz::isConsole() )
        {
            // return preg_replace('/[[:cntrl:]]/', '', $text);
            return preg_replace('/[[:^print:]]/', '', $text);
        }

        // return preg_replace('/[[:cntrl:]]/u', '', $text);
        return preg_replace('/[[:^print:]]/u', '', $text);
    }


    /**
     * Remove non printable characters from a string
     *
     * @see https://github.com/bcit-ci/CodeIgniter/blob/b862664f2ce2d20382b9af5bfa4dd036f5755409/system/core/Common.php
     */
    public static function removeInvisibleCharacters(string $str, bool $is_url_encoded = true) : string
    {
        $non_displayables = [];

        // every control character except newline (dec 10),
        // carriage return (dec 13) and horizontal tab (dec 09)
        if ( $is_url_encoded )
        {
            $non_displayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $non_displayables[] = '/%1[0-9a-f]/i';  // url encoded 16-31
            $non_displayables[] = '/%7f/i'; // url encoded 127
        }

        $non_displayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';   // 00-08, 11, 12, 14-31, 127

        do
        {
            $str = preg_replace($non_displayables, '', $str, -1, $count);
        }
        while ( $count );

        return $str;
    }


    /**
     * Counts the number of characters in an UTF-8 string.
     */
    public static function strlen(string $text, bool $is_use_mbstring = true, bool $is_html = false) : int
    {
        if ( $is_html )
        {
            $text = self::cleanHtml($text);
        }

        if ( $is_use_mbstring )
        {
            return mb_strlen($text);
        }

        // Do not count UTF-8 continuation bytes.
        return strlen(preg_replace("", '', $text));
    }


    /**
     * Get part of an UTF-8 string.
     */
    public static function substr(string $text, bool $start, ?int $length = null, bool $is_use_mbstring = true, bool $is_html = false) : string
    {
        if ( $is_html )
        {
            $text = self::cleanHtml($text);
        }

        if ( $is_use_mbstring )
        {
            return mb_substr($text, $start, $length);
        }

        // Do not count UTF-8 continuation bytes.
        return substr(preg_replace("", '', $text), $start, $length);
    }


    /**
     * Parse a URL query string encoded to an array
     *
     * @see https://www.php.net/manual/es/function.parse-str.php
     */
    public static function parse_str(string $text, bool $is_use_mb_parse_str = true) : array
    {
        $vec_params = [];
        if ( $is_use_mb_parse_str )
        {
            mb_parse_str($text, $vec_params);
        }
        else
        {
            parse_str($text, $vec_params);
        }

        return $vec_params;
    }


    /**
     * Checks whether a string is valid UTF-8.
     *
     * All functions designed to filter input should use drupal_validate_utf8 to ensure they operate on valid UTF-8 strings to prevent bypass of the filter.
     *
     * When text containing an invalid UTF-8 lead byte (0xC0 - 0xFF) is presented as UTF-8 to Internet Explorer 6, the program may misinterpret subsequent bytes. When these subsequent bytes are HTML control characters such as quotes or angle brackets, parts of the text that were deemed safe by filters end up in locations that are potentially unsafe; An onerror attribute that is outside of a tag, and thus deemed safe by a filter, can be interpreted by the browser as if it were inside the tag.
     *
     * The function does not return false for strings containing character codes above U+10FFFF, even though these are prohibited by RFC 3629.
     *
     * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Unicode.php/function/Unicode%3A%3AvalidateUtf8/8.2.x
     */
    public static function validateUtf8(string $text) : bool
    {
        if ( strlen($text) == 0 )
        {
            return true;
        }

        // With the PCRE_UTF8 modifier 'u', preg_match() fails silently on strings
        // containing invalid UTF-8 byte sequences. It does not reject character
        // codes above U+10FFFF (represented by 4 or more octets), though.
        return preg_match('/^./us', $text) == 1;
    }


    /**
     * Flip U+C0-U+DE to U+E0-U+FD and back. Can be used as preg_replace callback.
     *
     * @see https://api.drupal.org/api/drupal/core%21lib%21Drupal%21Component%21Utility%21Unicode.php/function/Unicode%3A%3AcaseFlip/8.2.x
     */
    public static function unicodeCaseflip(string $matches) : string
    {
        return $matches[0][0] . chr(ord($matches[0][1]) ^ 32);
    }


    /**
     * Splits a string into chunks on a given delimiter.
     *
     * @param string $string The string
     * @param string $delimiter The delimiter to split the string on (defaults to a comma)
     * @return string[] The segments of the string
     *
     * @see craft\helpers\StringHelper
     */
    public static function split(string $string, string $delimiter = ',')
    {
        return preg_split('/\s*' . preg_quote($delimiter, '/') . '\s*/', $string, -1, PREG_SPLIT_NO_EMPTY);
    }


    /**
     * Generates a random string of latin alphanumeric characters that defaults to a $length of 36.
     * If $is_extended_chars set to true, additional symbols can be included in the string.
     *
     * @see https://github.com/craftcms/cms/blob/9a7b018de6e003c3d3d129dc0391671b74d71635/src/helpers/StringHelper.php
     */
    public static function randomString(int $length = 36, bool $is_extended_chars = false) : string
    {
        // return Yii::$app->security->generateRandomString($length);

        if ( $is_extended_chars )
        {
            $valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890`~!@#$%^&*()-_=+[]\{}|;:\'",./<>?"';
        }
        else
        {
            $valid_chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        }

        return self::randomStringWithChars($valid_chars, $length);
    }


    /**
     * Generates a random string of character
     *
     * @see https://github.com/craftcms/cms/blob/9a7b018de6e003c3d3d129dc0391671b74d71635/src/helpers/StringHelper.php
     */
    public static function randomStringWithChars(string $valid_chars, int $length) : string
    {
        $output = '';

        // Count the number of chars in the valid chars string so we know how many choices we have
        $num_valid_chars = self::strlen($valid_chars);

        // PHP5.x Polyfill for random_int() function. Available from PHP 7.0 version
        // require_once Yii::getAlias("@lib.random_compat.lib") . DIRECTORY_SEPARATOR . "random.php";

        // Repeat the steps until we've created a string of the right length
        for ( $i = 0; $i < $length; $i++ )
        {
            // Pick a random number from 1 up to the number of valid chars
            $random_pick = random_int(1, $num_valid_chars);

            // Take the random character out of the string of valid chars
            $random_char = $valid_chars[$random_pick - 1];

            // add the randomly-chosen char onto the end of our string
            $output .= $random_char;
        }

        return $output;
    }


    /**
     * Generate a timestamp (YmdHi) with a random string prefix/suffix
     *
     * Example: 201909181556_f4R2EY96sD
     */
    public static function randomTimestamp(string $position = 'suffix', int $length = 10, string $separator = '_') : string
    {
        if ( $position === 'suffix' )
        {
            return date('YmdHi') . $separator . strtolower(self::randomString($length));
        }

        return strtolower(self::randomString($length)) . $separator . date('YmdHi');
    }


    /**
     * Encrypts a string value
     */
    public static function encrypt(string $string = '', ?string $hash_method = null) : string
    {
        // Unless, MD5 will be the default method
        if ( empty($hash_method) )
        {
            $hash_method = 'md5';
        }


        switch ( $hash_method )
        {
            case 'md5':
                return \md5($string);
                break;

            case 'sha1':
                return \sha1($string);
                break;

            default:
                return \hash($hash_method, $string);
                break;
        }
    }


    /**
     * Check if a strnig is a valid JSON
     */
    /*
    public static function isJson($string)
    {
        $result = json_decode($string);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    */


    /**
     * Returns is the given string matches a v4 UUID pattern.
     *
     * Version 4 UUIDs have the form xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx where x
     * is any hexadecimal digit and y is one of 8, 9, A, or B.
     *
     * @param string $uuid The string to check.
     * @return bool Whether the string matches a v4 UUID pattern.
     */
    public static function isUUID($uuid) : bool
    {
        return ! empty($uuid) && preg_match('/^' . self::UUID_PATTERN . '$/', $uuid);
    }


    /**
     * Generates a valid v4 UUID string. See [http://stackoverflow.com/a/2040279/684]
     *
     * @return string The UUID
     */
    public static function UUID() : string
    {
        // PHP 7 or greater
        if ( PHP_MAJOR_VERSION >= 7 )
        {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                // 16 bits for "time_mid"
                random_int(0, 0xffff),
                // 16 bits for "time_hi_and_version", four most significant bits holds version number 4
                random_int(0, 0x0fff) | 0x4000,
                // 16 bits, 8 bits for "clk_seq_hi_res", 8 bits for "clk_seq_low", two most significant bits holds zero and
                // one for variant DCE1.1
                random_int(0, 0x3fff) | 0x8000,
                // 48 bits for "node"
                random_int(0, 0xffff),
                random_int(0, 0xffff),
                random_int(0, 0xffff)
            );
        }

        // PHP 5.6 or lower
        else
        {
            return sprintf(
                '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
                // 32 bits for "time_low"
                mt_rand( 0, 0xffff ),
                mt_rand( 0, 0xffff ),

                // 16 bits for "time_mid"
                mt_rand( 0, 0xffff ),

                // 16 bits for "time_hi_and_version",
                // four most significant bits holds version number 4
                mt_rand( 0, 0x0fff ) | 0x4000,

                // 16 bits, 8 bits for "clk_seq_hi_res",
                // 8 bits for "clk_seq_low",
                // two most significant bits holds zero and one for variant DCE1.1
                mt_rand( 0, 0x3fff ) | 0x8000,

                // 48 bits for "node"
                mt_rand( 0, 0xffff ),
                mt_rand( 0, 0xffff ),
                mt_rand( 0, 0xffff )
            );
        }
    }


    /**
     * Validate an email address
     */
    public static function validateEmail(string $email) : bool
    {
        $validator = new \yii\validators\EmailValidator();
        return $validator->validate($email, $error);
    }


    /**
     * Turn all URLs in clickable links.
     *
     * @param string $value
     * @param array  $protocols  http/https, ftp, mail, twitter
     * @param array  $attributes
     * @return string
     *
     * @see https://gist.github.com/jasny/2000705
     */
    public static function linkify($value, $protocols = ['http', 'mail'], array $attributes = [])
    {
        // Link attributes
        $attr = '';
        foreach ( $attributes as $key => $val )
        {
            $attr .= ' ' . $key . '="' . htmlentities($val) . '"';
        }

        $links = [];

        // Extract existing links and tags
        $value = preg_replace_callback('~(<a .*?>.*?</a>|<.*?>)~i', function ($match) use (&$links) {
            return '<' . array_push($links, $match[1]) . '>';
        }, $value);

        // Extract text links for each protocol
        foreach ( (array) $protocols as $protocol )
        {
            switch ( $protocol )
            {
                case 'http':
                case 'https':
                    $value = preg_replace_callback('~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                        if ( $match[1] )
                        {
                            $protocol = $match[1];
                        }
                        $link = $match[2] ?: $match[3];
                        return '<' . array_push($links, "<a $attr href=\"$protocol://$link\">$link</a>") . '>';
                    }, $value);
                break;

                case 'mail':
                    $value = preg_replace_callback('~([^\s<]+?@[^\s<]+?\.[^\s<]+)(?<![\.,:])~', function ($match) use (&$links, $attr) {
                        return '<' . array_push($links, "<a $attr href=\"mailto:{$match[1]}\">{$match[1]}</a>") . '>';
                    }, $value);
                break;

                case 'twitter':
                    $value = preg_replace_callback('~(?<!\w)[@#](\w++)~', function ($match) use (&$links, $attr) {
                        return '<' . array_push($links, "<a $attr href=\"https://twitter.com/" . ($match[0][0] == '@' ? '' : 'search/%23') . $match[1] . "\">{$match[0]}</a>") . '>';
                    }, $value);
                break;

                default:
                    $value = preg_replace_callback('~' . preg_quote($protocol, '~') . '://([^\s<]+?)(?<![\.,:])~i', function ($match) use ($protocol, &$links, $attr) {
                        return '<' . array_push($links, "<a $attr href=\"$protocol://{$match[1]}\">{$match[1]}</a>") . '>';
                    }, $value);
                break;
            }
        }

        // Insert all link
        return preg_replace_callback('/<(\d+)>/', function ($match) use (&$links) {
            return $links[$match[1] - 1];
        }, $value);
    }


    /**
     * Returns a camelCase version of the given string. Trims surrounding spaces, capitalizes letters following digits,
     * spaces, dashes and underscores, and removes spaces, dashes, as well as underscores.
     */
    public static function camelCase(string $str): string
    {
        return (string)BaseStringy::create($str)->camelize();
    }


    /**
     * Converts a string to snake_cases format
     *
     * @param string $str The string to snakeize.
     * @return string The snakeized string.
     */
    public static function snakeCase(string $str): string
    {
        return (string)BaseStringy::create($str)->snakeize();
    }


    /**
     * Converts a string to PascalCases format
     *
     * @param string $str The string to process.
     * @return string
     */
    public static function pascalCase(string $str): string
    {
        $words = self::toWords($str, true, true);
        return implode('', array_map([
            static::class,
            'upperCaseFirst',
        ], $words));
    }


    /**
     * Converts the first character of the supplied string to uppercase.
     *
     * @param string $str The string to modify.
     * @return string The string with the first character being uppercase.
     */
    public static function upperCaseFirst(string $str): string
    {
        return (string)BaseStringy::create($str)->upperCaseFirst();
    }


    /**
     * Returns whether the given string has any lowercase characters in it.
     */
    public static function hasLowerCase(string $str): bool
    {
        return BaseStringy::create($str)->hasLowerCase();
    }


    /**
     * Returns whether the given string has any uppercase characters in it.
     */
    public static function hasUpperCase(string $str): bool
    {
        return BaseStringy::create($str)->hasUpperCase();
    }


    /**
     * Convert all HTML entities to their applicable characters.
     *
     * @param string $str The string to process.
     * @param int $flags A bitmask of these flags: https://www.php.net/manual/en/function.html-entity-decode.php
     * @return string The decoded string.
     */
    public static function htmlDecode(string $str, int $flags = ENT_COMPAT): string
    {
        return (string)BaseStringy::create($str)->htmlDecode($flags);
    }

    /**
     * Convert all applicable characters to HTML entities.
     *
     * @param string $str The string to process.
     * @param int $flags A bitmask of these flags: https://www.php.net/manual/en/function.html-entity-encode.php
     * @return string The encoded string.
     */
    public static function htmlEncode(string $str, int $flags = ENT_COMPAT): string
    {
        return (string)BaseStringy::create($str)->htmlEncode($flags);
    }


    /**
     * Returns true if the string contains only alphabetic chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only alphabetic chars.
     */
    public static function isAlpha(string $str): bool
    {
        return BaseStringy::create($str)->isAlpha();
    }

    /**
     * Returns true if the string contains only alphabetic and numeric chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only alphanumeric chars.
     */
    public static function isAlphanumeric(string $str): bool
    {
        return BaseStringy::create($str)->isAlphanumeric();
    }

    /**
     * Returns true if the string is base64 encoded, false otherwise.
     *
     * @param string $str The string to check.
     * @param bool $emptyStringIsValid Whether or not an empty string is considered valid.
     * @return bool Whether or not $str is base64 encoded.
     * @since 3.3.0
     */
    public static function isBase64(string $str, bool $emptyStringIsValid = true): bool
    {
        return BaseStringy::create($str)->isBase64($emptyStringIsValid);
    }

    /**
     * Returns true if the string contains only whitespace chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only whitespace characters.
     * @since 3.3.0
     */
    public static function isBlank(string $str): bool
    {
        return BaseStringy::create($str)->isBlank();
    }


    /**
     * Returns true if the string contains only hexadecimal chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only hexadecimal chars.
     * @since 3.3.0
     */
    public static function isHexadecimal(string $str): bool
    {
        return BaseStringy::create($str)->isHexadecimal();
    }


    /**
     * Returns true if the string contains HTML-Tags, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains HTML tags.
     */
    public static function isHtml(string $str): bool
    {
        return BaseStringy::create($str)->isHtml();
    }


    /**
     * Returns true if the string is JSON, false otherwise. Unlike json_decode
     * in PHP 5.x, this method is consistent with PHP 7 and other JSON parsers,
     * in that an empty string is not considered valid JSON.
     *
     * @param string $str The string to check.
     * @param bool $onlyArrayOrObjectResultsAreValid
     * @return bool Whether or not $str is JSON.
     */
    public static function isJson(string $str, bool $onlyArrayOrObjectResultsAreValid = false): bool
    {
        return BaseStringy::create($str)->isJson($onlyArrayOrObjectResultsAreValid);
    }

    /**
     * Returns true if the string contains only lower case chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str is only lower case characters.
     */
    public static function isLowerCase(string $str): bool
    {
        return BaseStringy::create($str)->isLowerCase();
    }


    /**
     * Returns true if the string is serialized, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str is serialized.
     * @since 3.3.0
     */
    public static function isSerialized(string $str): bool
    {
        return BaseStringy::create($str)->isSerialized();
    }


    /**
     * Returns true if the string contains only upper case chars, false
     * otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only lower case characters.
     */
    public static function isUpperCase(string $str): bool
    {
        return BaseStringy::create($str)->isUpperCase();
    }


    /**
     * Checks if the given string is UTF-8 encoded.
     *
     * @param string $str The string to check.
     * @return bool Whether the string was UTF encoded or not.
     */
    public static function isUtf8(string $str): bool
    {
        return static::encoding($str) === 'utf-8';
    }


    /**
     * Returns true if the string contains only whitespace chars, false otherwise.
     *
     * @param string $str The string to check.
     * @return bool Whether or not $str contains only whitespace characters.
     */
    public static function isWhitespace(string $str): bool
    {
        return BaseStringy::create($str)->isBlank();
    }


    /**
     * Returns an array of words extracted from a string
     *
     * @param string $str The string
     * @param bool $lower Whether the returned words should be lowercased
     * @param bool $removePunctuation Whether punctuation should be removed from the returned words
     * @return string[] The prepped words in the string
     */
    public static function toWords(string $str, bool $lower = false, bool $removePunctuation = false): array
    {
        // Convert CamelCase to multiple words
        // Regex copied from Inflector::camel2words(), but without dropping punctuation
        $str = preg_replace('/(?<!\p{Lu})(\p{Lu})|(\p{Lu})(?=\p{Ll})/u', ' \0', $str);

        if ( $lower )
        {
            // Make it lowercase
            $str = mb_strtolower($str);
        }

        if ( $removePunctuation )
        {
            $str = str_replace(['.', '_', '-'], ' ', $str);
        }

        // Remove inner-word punctuation.
        $str = preg_replace('/[\'"‘’“”\[\]\(\)\{\}:]/u', '', $str);

        // Split on the words and return
        return static::splitOnWords($str);
    }


    /**
     * Splits a string into an array of the words in the string.
     *
     * @param string $str The string
     * @return string[] The words in the string
     */
    public static function splitOnWords(string $str): array
    {
        // Split on anything that is not alphanumeric, or a period, underscore, or hyphen.
        // Reference: http://www.regular-expressions.info/unicode.html
        preg_match_all('/[\p{L}\p{N}\p{M}\._-]+/u', $str, $matches);

        return ArrayHelper::filterEmptyStringsFromArray($matches[0]);
    }
}
