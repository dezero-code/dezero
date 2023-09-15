<?php
/**
 * Flash messages manager trait class
 *
 * @see dezero\traits\ErrorTrait
 */

namespace dezero\traits;

use dezero\helpers\ArrayHelper;
use Yii;

trait FlashMessageTrait
{
    /**
     * Flash messages
     */
    private $vec_messages = [];


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


    /**
     * Show flash messages: ['success', 'warning', 'error']
     */
    public function showFlashMessages() : void
    {
        $vec_classes = ['success', 'info', 'warning', 'error'];
        foreach ( $vec_classes as $class_type )
        {
            $vec_messages = $this->getFlashMessages($class_type);
            if ( !empty($vec_messages) )
            {
                Yii::$app->session->setFlash($class_type, $vec_messages);
            }
        }
    }
}
