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
}
