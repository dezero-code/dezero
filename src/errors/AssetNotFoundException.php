<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\errors;

use dezero\errors\AssetException;

/**
 * AssetNotFoundException represents an exception caused by asset file or asset directory not found.
 */
class AssetNotFoundException extends AssetException
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Asset file or asset directory Not Found';
    }
}
