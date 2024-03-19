<?php
/**
 * ActiveDataProvider class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\data;

use Yii;

/**
 * ActiveDataProvider implements a data provider based on [[\yii\db\Query]] and [[\yii\db\ActiveQuery]].
 */
class ActiveDataProvider extends \yii\data\ActiveDataProvider
{
    /**
     * @{inheritdoc}
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Check if returned data models is empty
     */
    public function isEmpty() : bool
    {
        return $this->getTotalCount() === 0;
    }
}
