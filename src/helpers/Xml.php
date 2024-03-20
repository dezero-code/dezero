<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\helpers;

use dezero\helpers\Json;
use dezero\helpers\StringHelper;

/**
 * Helper for working with XML structure
 */
class Xml
{
    /**
     * Converts a XML string to a PHP array using SimpleXML library
     */
    static public function toArray(string $xml) : array
    {
        $simple_xml = simplexml_load_string($raw_xml_string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ( empty($simple_xml) )
        {
            return [];
        }

        $vec_data = Json::decode(Json::encode((array)$simple_xml));

        return self::cleanArray($vec_data);
    }


    /**
     * Clean (removes) empty values on an array
     *
     * <PRODUCT></PRODUCT> turns into [PRODUCT] => Array(0 => '')
     */
    static public function cleanArray(array $vec_data) : array
    {
        if ( empty($vec_data) )
        {
            return [];
        }

        foreach ( $vec_data as $tag_name => $tag_value )
        {
            if ( ! is_array($tag_value) )
            {
                continue;
            }

            if ( isset($tag_value[0]) && count($tag_value) == 1 && empty(StringHelper::trim($tag_value[0])) )
            {
                $vec_data[$tag_name] = '';
            }
            else
            {
                $vec_data[$tag_name] = self::cleanArray($tag_value);
            }
        }

        return $vec_data;
    }


    /**
     * Remove white spaces
     *
     * First version (used here) extracted from BASIPIM when exporting data to XML files
     *
     * Second version in https://pageconfig.com/post/remove-undesired-characters-with-trim_all-php
     */
    public static function trimAll(string $text) : string
    {
        return StringHelper::trimAll($text);
    }


    /**
     * Escape XML strings
     *
     * @see http://stackoverflow.com/questions/3426090/how-do-you-make-strings-xmlsafe
     */
    public static function escape(string $xml_content, bool $is_cdata = false, bool $is_html = true) : string
    {
        $xml_content = StringHelper::trim($xml_content);
        if ( $xml_content === '' )
        {
            return '';
        }

        // Remove all non printables ASCII characets
        // @see http://stackoverflow.com/questions/1176904/php-how-to-remove-all-non-printable-characters-in-a-string
        // $xml_content = preg_replace('/[^[:print:]]/', '', $xml_content);

        // Remove invisible characters and all the CONTROL characters
        $xml_content = StringHelper::cleanText($xml_content);


        // Convert XML strings
        if ( ! $is_cdata )
        {
            return htmlspecialchars($xml_content, ENT_XML1, 'UTF-8');
        }

        // CDATA - HTML tags is not allowed
        if ( ! $is_html )
        {
            $xml_content = StringHelper::cleanHtml($xml_content);
        }

        // CDATA - HTML tags is allowed. Let's clean HTML tags
        else
        {
            $xml_content = str_replace(['<span></span>', '<p></p>', '<li></li>', ''], '', $xml_content);
            $xml_content = str_replace(['<br></p>'], '</p>', $xml_content);
            $xml_content = str_replace(['<p><br></p>'], '<br>', $xml_content);
        }

        return '<![CDATA['. $xml_content .']]>';
    }
}
