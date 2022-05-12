<?php
/**
 * Config class file based on crisu83.yii-consoletools.helpers package
 *
 * Helper class for working with several config files
 */

namespace dezero\helpers;

use Yii;
use yii\helpers\ArrayHelper;

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
