<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\widgets;

use yii\widgets\Pjax;
use Yii;

/**
 * Pjax is a widget integrating the [pjax](https://github.com/yiisoft/jquery-pjax) jQuery plugin.
 */
class GridViewPjax extends \yii\widgets\Pjax
{
    /**
     * @var string The GridView id
     */
    public $gridview;


    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->options['class'] = 'dz-grid-container';
        if ( !empty($this->gridview) )
        {
            $this->options['id'] = $this->gridview .'-container';
        }

        parent::init();
    }
}
