<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\errors;

use yii\base\Exception;

/**
 * AssetException represents an exception related to an asset file or asset directory.
 */
class AssetException extends Exception
{
    /**
     * @return string the user-friendly name of this exception
     */
    public function getName()
    {
        return 'Asset Exception';
    }
}
