<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\events;

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
}
