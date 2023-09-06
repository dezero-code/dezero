<?php
/**
 * Custom exception class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 */

namespace dezero\errors;

use yii\base\Exception;

class QueryAbortedException extends Exception
{
    /**
     * @return string The user-friendly name of this exception
     */
    public function getName(): string
    {
        return 'Query Aborted Exception';
    }
}
