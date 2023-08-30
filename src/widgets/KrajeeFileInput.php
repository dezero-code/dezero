<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\widgets;

use dezero\helpers\ArrayHelper;
use dezero\helpers\Html;
use dezero\helpers\Url;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\asset\assets\FileinputAsset;
use kartik\file\FileInput;
use Yii;

/**
 * FileInput widget by Krajee
 *
 * @see https://github.com/kartik-v/yii2-widget-fileinput
 * @see https://demos.krajee.com/widget-details/fileinput
 */
class KrajeeFileInput extends FileInput
{
    /**
     * Initializes the widget.
     */
    public function init()
    {
        // Force Bootstrap version to 4.x
        $this->bsVersion = '4.x';

        parent::init();

        // Default icons
        $this->pluginOptions['browseIcon'] = '<i class="wb-folder"></i>';
        $this->pluginOptions['cancelIcon'] = '<i class="wb-close"></i>';
        $this->pluginOptions['removeIcon'] = '<i class="wb-trash"></i>';
        $this->pluginOptions['uploadIcon'] = '<i class="wb-upload"></i>';
        $this->pluginOptions['previewFileIcon'] = '<i class="wb-file"></i>';
        $this->pluginOptions['msgValidationErrorIcon'] = '<i class="wb-warning"></i>';

        // File actions (preview before upload)
        $this->pluginOptions['fileActionSettings'] = [
            'showZoom'          => false,
            'showRemove'        => false,
            'removeClass'       => 'hide',
            'showRotate'        => false,
            'showDrag'          => false,
            'showDownload'      => true,

            // Icons when image/file is selected
            'zoomIcon'          => '<i class="wb-zoom-in"></i>',
            'removeIcon'        => '<i class="wb-trash"></i>',
            'uploadIcon'        => '<i class="wb-upload"></i>',
            'downloadIcon'      => '<i class="wb-download"></i>',
            'uploadRetryIcon'   => '<i class="wb-replay"></i>',
            'dragIcon'          => '<i class="wb-move"></i>',
        ];

        // Modal actions (preview Zoom clicked)
        $this->pluginOptions['previewZoomButtonIcons'] = [
          'prev'            => '<i class="wb-chevron-left"></i>',
          'next'            => '<i class="wb-chevron-right"></i>',
          'rotate'          => '<i class="wb-replay"></i>',
          'toggleheader'    => '<i class="wb-arrow-expand"></i>',
          'fullscreen'      => '<i class="wb-expand"></i>',
          'borderless'      => '<i class="wb-arrow-shrink"></i>',
          'close'           => '<i class="wb-close"></i>'
        ];

        // Events
        $this->pluginEvents['fileclear'] = '$.dezeroFileinput.fileclear';
    }


    /**
     * {@inheritdoc}
     */
    public function registerAssets()
    {
        parent::registerAssets();

        // Register custom Javascript
        $view = $this->getView();
        FileinputAsset::register($view);
    }


    /**
     * {@inheritdoc}
     */
    protected function initWidget()
    {
        if ( $this->hasModel() && !empty($this->value) )
        {
            // Check if multiple option is enabled
            $is_multiple = ArrayHelper::getValue($this->options, 'multiple') && !ArrayHelper::getValue($this->pluginOptions, 'uploadUrl');
            if ( ! $is_multiple && is_numeric($this->value) )
            {
                // File exist, show a preview
                $asset_file_model = AssetFile::findOne($this->value);
                if ( $asset_file_model )
                {
                    if ( $asset_file_model->isImage() )
                    {
                        $this->pluginOptions['initialPreviewAsData'] = true;
                        $this->pluginOptions['initialPreview'] = [
                            $asset_file_model->url()
                        ];
                    }
                    else
                    {
                        $this->pluginOptions['preferIconicPreview'] = true;
                        $this->pluginOptions['initialPreviewAsData'] = false;
                        $this->pluginOptions['initialPreview'] = [
                            '<div class="file-preview-other"><span class="file-other-icon"><i class="wb-file"></i></span></div>'
                        ];
                    }

                    $this->pluginOptions['overwriteInitial'] = true;
                    $this->pluginOptions['initialPreviewConfig'] = [
                        [
                            'caption'       => $asset_file_model->file_name,
                            'size'          => $asset_file_model->file_size,
                            'downloadUrl'   => $asset_file_model->url()
                        ]
                    ];

                }

                $this->options['hiddenOptions']['id'] = Html::getInputId($this->model, $this->attribute) .'-hidden';
                $this->options['hiddenOptions']['value'] = $this->value;
            }
        }

        return parent::initWidget();
    }
}
