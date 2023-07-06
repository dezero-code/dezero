<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 *
 * @see https://github.com/dmstr/yii2-adminlte-asset/blob/master/widgets/Alert.php
 */

namespace dezero\widgets;

use Yii;
use yii\bootstrap4\Alert as BootstrapAlert;
use yii\bootstrap4\Widget;

/**
 * Alert widget renders a message from session flash.
 * All flash messages are displayed in the sequence they were assigned
 * using setFlash. You can set message as following:
 *
 * ```php
 *  Yii::$app->session->setFlash('error', 'This is the message');
 *  Yii::$app->session->setFlash('success', 'This is the message');
 *  Yii::$app->session->setFlash('info', 'This is the message');
 * ```
 *
 * Multiple messages could be set as follows:
 *
 * ```php
 *  Yii::$app->session->setFlash('error', ['Error 1', 'Error 2']);
 * ```
 *
 */
class Alert extends Widget
{
    /**
     * @var array the alert types configuration for the flash messages.
     * This array is setup as $key => $value, where:
     * - $key is the name of the session flash variable
     * - $value is the array:
     *       - class of alert type (i.e. danger, success, info, warning)
     *       - icon for alert
     */
    public $alertTypes = [
        'error' => [
            'class' => 'alert-danger',
            'icon' => '<i class="icon wb-warning mr-10"></i>',
        ],
        'danger' => [
            'class' => 'alert-danger',
            'icon' => '<i class="icon wb-warning mr-10"></i>',
        ],
        'success' => [
            'class' => 'alert-success',
            'icon' => '<i class="icon wb-check mr-10"></i>',
        ],
        'info' => [
            'class' => 'alert-info',
            'icon' => '<i class="icon wb-info-circle mr-10"></i>',
        ],
        'warning' => [
            'class' => 'alert-warning',
            'icon' => '<i class="icon wb-alert-circle mr-10"></i>',
        ],
    ];

    /**
     * @var array the options for rendering the close button tag.
     */
    public $closeButton = [];


    /**
     * @var boolean whether to removed flash messages during AJAX requests
     */
    public $isAjaxRemoveFlash = true;


    /**
     * Initializes the widget.
     * This method will register the bootstrap asset bundle. If you override this method,
     * make sure you call the parent implementation first.
     */
    public function init()
    {
        parent::init();

        if ( !isset($this->options['class']) )
        {
            $this->options['class'] = 'alert in dark alert-dissimible alert-block';
        }

        $session = Yii::$app->getSession();
        $vec_flashes = $session->getAllFlashes();
        $append_css = isset($this->options['class']) ? ' ' . $this->options['class'] : '';

        foreach ( $vec_flashes as $type => $data )
        {
            if ( isset($this->alertTypes[$type]) )
            {
                $data = (array) $data;
                foreach ( $data as $message )
                {
                    // initialize css class for each alert box
                    $this->options['class'] = $this->alertTypes[$type]['class'] . $append_css;

                    // Assign unique id to each alert box
                    $this->options['id'] = $this->getId() . '-' . $type;

                    echo BootstrapAlert::widget([
                        'body'          => $this->alertTypes[$type]['icon'] . $message,
                        'closeButton'   => $this->closeButton,
                        'options'       => $this->options,
                    ]);
                }

                if ( $this->isAjaxRemoveFlash && !Yii::$app->request->isAjax )
                {
                    $session->removeFlash($type);
                }
            }
        }
    }
}
