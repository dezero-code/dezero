<?php
/**
 * Warning manager trait class
 *
 * @see dezero\traits\ErrorTrait
 */

namespace dezero\traits;

use dezero\helpers\ArrayHelper;
use Yii;

trait WarningTrait
{
    /**
     * Registered warnings
     */
    public $vec_warnings = [];


    /**
     * @return array
     */
    public function getWarnings() : array
    {
        return $this->vec_warnings;
    }


    /**
     * @return bool
     */
    public function hasWarnings() : bool
    {
        return !empty($this->vec_warnings);
    }


    /**
     * Add warning(s)
     */
    public function addWarning($vec_warnings) : void
    {
        if ( is_array($vec_warnings) )
        {
            $this->vec_warnings = ArrayHelper::merge($this->vec_warnings, $vec_warnings);
        }
        else
        {
            $this->vec_warnings[] = $vec_warnings;
        }
    }


    /**
     * Show warning(s)
     */
    public function showWarnings() : void
    {
        // Show warnings
        $vec_warnings = $this->getWarnings();
        if ( !empty($vec_warnings) )
        {
            Yii::$app->session->setFlash('warning', $vec_warnings);
        }

        // Get warnings from Flash Messages
        if ( method_exists($this, 'getFlashMessages') )
        {
            $vec_warnings = $this->getFlashMessages('warning');
            if ( !empty($vec_warnings) )
            {
                Yii::$app->session->setFlash('warning', $vec_warnings);
            }
        }
    }
}
