<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\entity;

use Dz;
use Yii;

/**
 * Trait class to implement enable process
 */
trait EnableTrait
{
    /**
     * Checks if an entity is enabled
     */
    public function isEnabled()
    {
        if ( ! $this->hasAttribute('disabled_date') )
        {
            return false;
        }

        if ( $this->hasAttribute('is_disabled') )
        {
            return $this->is_disabled == 0;
        }

        return empty($this->disabled_date);
    }


    /**
     * Enables an entity
     */
    public function enable()
    {
        if ( ! $this->hasAttribute('disabled_date') || ! $this->hasAttribute('disabled_user_id') )
        {
            return false;
        }

        $this->scenario = 'enable';
        $this->disabled_date = null;
        $this->disabled_uid = null;

        // Set "is_disabled" attribute
        if ( $this->hasAttribute('is_disabled') )
        {
            $this->is_disabled = 0;
        }

        return $this->save();
    }
}

