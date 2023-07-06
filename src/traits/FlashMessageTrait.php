<?php
/**
 * Flash messages manager trait class
 *
 * @see dezero\traits\ErrorTrait
 */

namespace dezero\traits;

use dezero\helpers\ArrayHelper;

trait FlashMessageTrait
{
    /**
     * Flash messages
     */
    public $vec_messages = [];


    /**
     * @return array
     */
    public function getFlashMessages($type = 'success') : array
    {
        return isset($this->vec_messages[$type]) ? $this->vec_messages[$type] : [];
    }


    /**
     * Add message(s)
     */
    public function addFlashMessage($vec_messages, $type = 'success') : void
    {
        if ( !isset($this->vec_messages[$type]) )
        {
            $this->vec_messages[$type] = [];
        }

        if ( is_array($vec_messages) )
        {
            $this->vec_messages[$type] = ArrayHelper::merge($this->vec_messages[$type], $vec_messages);
        }
        else
        {
            $this->vec_messages[$type][] = $vec_messages;
        }
    }
}
