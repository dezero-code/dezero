<?php
/**
 * Error manager trait class
 *
 * @see dezero\traits\FlashMessageTrait
 */

namespace dezero\traits;

use dezero\helpers\ArrayHelper;
use Yii;

trait ErrorTrait
{
    /**
     * Registered errors
     */
    private $vec_errors = [];


    /**
     * @return array
     */
    public function getErrors() : array
    {
        return $this->vec_errors;
    }


    /**
     * Add error(s)
     */
    public function addError($vec_errors) : void
    {
        if ( is_array($vec_errors) )
        {
            $this->vec_errors = ArrayHelper::merge($this->vec_errors, $vec_errors);
        }
        else
        {
            $this->vec_errors[] = $vec_errors;
        }
    }


    /**
     * Show error(s)
     */
    public function showErrors() : void
    {
        // Show errors
        $vec_errors = $this->getErrors();
        if ( !empty($vec_errors) )
        {
            Yii::$app->session->setFlash('error', $vec_errors);
        }

        // Get errors from Flash Messages
        if ( method_exists($this, 'getFlashMessages') )
        {
            $vec_errors = $this->getFlashMessages('error');
            if ( !empty($vec_errors) )
            {
                Yii::$app->session->setFlash('error', $vec_errors);
            }
        }
    }
}
