<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\events;

use Yii;
use yii\base\Event;
use yii\base\Model;


/**
 * Form event class
 */
class FormEvent extends Event
{
    // const EVENT_BEFORE_REQUEST = 'beforeRequest';
    // const EVENT_AFTER_REQUEST = 'afterRequest';
    // const EVENT_BEFORE_RESEND = 'beforeResend';
    // const EVENT_AFTER_RESEND = 'afterResend';
    // const EVENT_BEFORE_REGISTER = 'beforeRegister';
    // const EVENT_AFTER_REGISTER = 'afterRegister';
    const EVENT_FAILED_LOGIN = 'failedLogin';
    const EVENT_BEFORE_LOGIN = 'beforeLogin';
    const EVENT_AFTER_LOGIN = 'afterLogin';


    /**
     * @var Model The form model associated with the event.
     */
    public $form;


    public function __construct(Model $form, array $config = [])
    {
        $this->form = $form;
        parent::__construct($config);
    }

    /**
     * Custom event when login is failed
     */
    /*
    public static function failedLogin(FormEvent $event)
    {
        \dezero\helpers\Log::dev("FormEventListener::onFailedLogin raised!");
    }
    */

    /**
     * Custom AFTER LOGIN event
     */
    public static function afterLogin(FormEvent $event)
    {
        $login_form = $event->form;
        $login_form->getUser()->updateAttributes([
            'last_login_date'   => time(),
            'last_login_ip'     => Yii::$app->request->getUserIP(),
        ]);
    }
}
