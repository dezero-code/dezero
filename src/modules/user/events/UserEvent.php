<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\events;

use dezero\modules\user\models\User;
use yii\base\Event;


/**
 * User event class
 */
class UserEvent extends Event
{
    // const EVENT_BEFORE_CREATE = 'beforeCreate';
    // const EVENT_AFTER_CREATE = 'afterCreate';
    // const EVENT_BEFORE_DELETE = 'beforeDelete';
    // const EVENT_AFTER_DELETE = 'afterDelete';
    // const EVENT_BEFORE_REGISTER = 'beforeRegister';
    // const EVENT_AFTER_REGISTER = 'afterRegister';
    // const EVENT_BEFORE_ACCOUNT_UPDATE = 'beforeUpdate';
    // const EVENT_AFTER_ACCOUNT_UPDATE = 'afterUpdate';
    // const EVENT_BEFORE_CONFIRMATION = 'beforeConfirmation';
    // const EVENT_AFTER_CONFIRMATION = 'afterConfirmation';
    // const EVENT_BEFORE_UNBLOCK = 'beforeUnblock';
    // const EVENT_AFTER_UNBLOCK = 'afterUnblock';
    // const EVENT_BEFORE_BLOCK = 'beforeBlock';
    // const EVENT_AFTER_BLOCK = 'afterBlock';
    const EVENT_BEFORE_LOGOUT = 'beforeLogout';
    const EVENT_AFTER_LOGOUT = 'afterLogout';


    /**
     * @var User The user model associated with the event.
     */
    public $user;


    public function __construct(User $user, array $config = [])
    {
        $this->user = $user;
        parent::__construct($config);
    }
}
