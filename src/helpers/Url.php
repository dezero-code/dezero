<?php
/**
 * Class UrlHelper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */


namespace dezero\helpers;

use dezero\helpers\ArrayHelper;
use Yii;

/**
 * Helper for creating application configurations.
 */
class Url extends \yii\helpers\Url
{
    /**
     * Creates a URL based on the given parameters.
     *
     * Overrided version allowing URL with this format:
     *
     * ```php
     * Url::to('product/product/update', ['id' => 5])
     * ```
     */
    public static function to($url = '', $scheme = false)
    {
        if ( is_array($scheme) && ! is_array($url) )
        {
            $url = ArrayHelper::merge([$url], $scheme);
        }

        // Force to be ABSOLUTE always
        return parent::to($url, true);
    }


    /**
     * Get current URL using HttpRequest class
     *
     * Examples:
     *  - $is_full_url = true  --> http://mysite.local/en/my-product
     *  - $is_full_url = false --> /en/my-product
     */
    public static function currentRequest($is_full_url = true)
    {
        return $is_full_url ? Yii::$app->request->hostInfo . Yii::$app->request->url : Yii::$app->request->url;
    }



    /**
     * Normalizes query string params
     *
     * @param string|array|null $params
     */
    public static function normalizeParams($params) : array
    {
        // If it's already an array, just split out the fragment and return
        if ( is_array($params) )
        {
            $fragment = ArrayHelper::remove($params, '#');
            return [$params, $fragment];
        }

        $fragment = null;

        if ( is_string($params) )
        {
            $params = ltrim($params, '?&');

            if ( ($fragmentPos = strpos($params, '#') ) !== false)
            {
                $fragment = substr($params, $fragmentPos + 1);
                $params = substr($params, 0, $fragmentPos);
            }

            parse_str($params, $vec_params);
        }
        else
        {
            $vec_params = [];
        }

        return [$vec_params, $fragment];
    }


    /**
     * Extracts the params and fragment from a given URL, and merges those with another set of params
     */
    public static function extractParams(string $url) : array
    {
        if ( ($queryPos = strpos($url, '?') ) === false && ($queryPos = strpos($url, '#') ) === false)
        {
            return [$url, [], null];
        }

        [$vec_params, $fragment] = self::normalizeParams(substr($url, $queryPos));

        return [substr($url, 0, $queryPos), $vec_params, $fragment];
    }
}
