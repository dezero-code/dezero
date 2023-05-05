<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\listeners;

use dezero\modules\user\events\FormEvent;

/**
 * UserListener class
 */
class UserListener
{
    public static function onFailedLogin(FormEvent $event)
    {
        \dezero\helpers\Log::dev("UserListener::onFailedLogin raised!");
    }
}
