<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\mymodule\events;

use dezero\modules\mymodule\models\Mymodule;
use yii\base\Event;


/**
 * Mymodule event class
 */
class MymoduleEvent extends Event
{
    const EVENT_BEFORE_CREATE = 'beforeCreate';
    const EVENT_AFTER_CREATE = 'afterCreate';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    const EVENT_AFTER_UPDATE = 'afterUpdate';

    /**
     * @var Mymodule The mymodule model associated with the event.
     */
    public $mymodule;


    public function __construct(Mymodule $mymodule, array $config = [])
    {
        $this->mymodule = $mymodule;
        parent::__construct($config);
    }
}
