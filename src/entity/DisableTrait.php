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
 * Trait class to implement disable process
 */
trait DisableTrait
{
    /**
     * Checks if an entity is disabled
     */
    public function isDisabled()
    {
        if ( ! $this->hasAttribute('disabled_date') )
        {
            return false;
        }

        if ( $this->hasAttribute('is_disabled') )
        {
            return $this->is_disabled == 1;
        }

        return !empty($this->disabled_date);
    }


    /**
     * Disables an entity
     */
    public function disable()
    {
        if ( ! $this->hasAttribute('disabled_date') || ! $this->hasAttribute('disabled_user_id') )
        {
            return false;
        }

        $this->disabled_date = time();
        $this->disabled_user_id = Dz::isConsole() ? 1 : Yii::$app->user->id;

        // Set "is_disabled" attribute
        if ( $this->hasAttribute('is_disabled') )
        {
            $this->is_disabled = 1;
        }

        return $this->save();
    }
}

