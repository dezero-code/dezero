<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\category\events;

use dezero\modules\category\models\Category;
use yii\base\Event;


/**
 * Category event class
 */
class CategoryEvent extends Event
{
    const EVENT_BEFORE_CREATE = 'beforeCreate';
    const EVENT_AFTER_CREATE = 'afterCreate';
    const EVENT_BEFORE_DELETE = 'beforeDelete';
    const EVENT_AFTER_DELETE = 'afterDelete';
    const EVENT_BEFORE_UPDATE = 'beforeUpdate';
    const EVENT_AFTER_UPDATE = 'afterUpdate';

    /**
     * @var Category The category model associated with the event.
     */
    public $category;


    public function __construct(Category $category, array $config = [])
    {
        $this->category = $category;
        parent::__construct($config);
    }
}
