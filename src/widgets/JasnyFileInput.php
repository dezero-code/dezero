<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\widgets;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\asset\assets\JasnyFileinputAsset;
use yii\widgets\InputWidget;
use Yii;

/**
 * FileInput widget by Jasny
 *
 * @see https://www.jasny.net/bootstrap/components/#fileinput
 * @see https://github.com/jasny/bootstrap
 * @see https://github.com/2amigos/yii2-file-input-widget
 */
class JasnyFileInput extends InputWidget
{
    public const STYLE_INPUT = 'input';
    public const STYLE_BUTTON = 'button';
    public const STYLE_IMAGE = 'image';


    /**
     * @var string. the type of Jasny File Input style to render
     */
    public $style;


    /**
     * @var array. Jasny File Upload event handlers
     */
    public $events = [];


    /**
     * Classes for buttons
     */
    public $vec_classes = [];


    /**
     * Title for buttons
     */
    public $vec_labels = [];



    /**
     * Initializes the widget.
     */
    public function init()
    {
        // Default classes for buttons
        if ( empty($this->vec_classes) )
        {
            $this->vec_classes = [
                'new'       => 'btn-default btn-outline',
                'remove'    => 'btn-default btn-outline',
                'select'    => 'btn-default btn-outline',
            ];
        }


        // Title for buttons
        if ( empty($this->vec_labels) )
        {
            $this->vec_labels = [
                'new'       => Yii::t('backend', 'Add file...'),
                'remove'    => Yii::t('backend', 'Remove'),
                'change'    => Yii::t('backend', 'Change'),
                'select'    => Yii::t('backend', 'Select image'),
            ];
        }

        parent::init();

        // Default style
        if ( $this->style === null )
        {
            $this->style = self::STYLE_INPUT;
        }
    }


    /**
     * {@inheritdoc}
     */
    public function run()
    {
        if ( $this->hasModel() )
        {
            $field = Html::activeFileInput($this->model, $this->attribute, $this->options);
        }
        else
        {
            $field = Html::fileInput($this->name, $this->value, $this->options);
        }

        echo $this->renderTemplate($field);

        $this->registerClientScript();
    }


    /**
     * Renders the template according
     */
    public function renderTemplate(string $field) : string
    {
        $view_path = $this->getViewPath();

        $vec_params = [
            'field'         => $field,
            'vec_classes'   => $this->vec_classes,
            'vec_labels'    => $this->vec_labels,
        ];

        // INPUT style
        $view = $view_path . '/jasny--input.tpl.php';

        // BUTTON style
        if ( $this->style === self::STYLE_BUTTON )
        {
            $view = $view_path . '/jasny--button.tpl.php';
        }

        // IMAGE style
        else if ( $this->style === self::STYLE_IMAGE )
        {
            $view = $view_path . '/jasny--image.tpl.php';
            $vec_params['thumbnail'] = $this->thumbnail;
        }

        return $this->getView()->renderFile(Yii::getAlias($view), $vec_params);
    }


    /**
     * Registers Jasny File Input Bootstrap plugin and the related events.
     */
    public function registerClientScript()
    {
        // Register custom Javascript
        $view = $this->getView();
        JasnyFileinputAsset::register($view);

        $id = $this->options['id'];

        // $options = !empty($this->clientOptions) ? Json::encode($this->clientOptions) : '';

        // $js[] = ";jQuery('#$id').fileinput({$options});";

        if ( !empty($this->events) )
        {
            $js = [];
            foreach ( $this->events as $event => $handler )
            {
                $js[] = ";jQuery('#$id').on('$event', $handler);";
            }
            $view->registerJs(implode("\n", $js));
        }
    }
}
