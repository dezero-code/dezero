<?php
/*
|--------------------------------------------------------------------------
| Use case "Deletes a file"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\asset\services;

use Dz;
use dezero\contracts\ServiceInterface;
// use dezero\modules\asset\events\AssetFileEvent;
use dezero\modules\asset\models\AssetFile;
use dezero\traits\ErrorTrait;
use dezero\traits\FlashMessageTrait;
use Yii;

class FileDeleteService implements ServiceInterface
{
    use ErrorTrait;
    use FlashMessageTrait;


    /**
     * Constructor
     */
    public function __construct(AssetFile $asset_file_model)
    {
        $this->asset_file_model = $asset_file_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Delete the AssetFile model
        if ( ! $this->deleteFile() )
        {
            return false;
        }

        return true;
    }


    /**
     * Delete the AssetFile model
     */
    private function deleteFile() : bool
    {
        // Custom event triggered on "beforeDelete"
        // $asset_file_event = Dz::makeObject(AssetFileEvent::class, [$this->asset_file_model]);
        // $this->asset_file_model->trigger(AssetFileEvent::EVENT_BEFORE_DELETE, $asset_file_event);

        if ( $this->asset_file_model->delete() !== false )
        {
            // Custom event triggered on "afterDelete"
            // $this->asset_file_model->trigger(AssetFileEvent::EVENT_AFTER_DELETE, $asset_file_event);

            return true;
        }

        // Error message
        $this->addError(Yii::t('backend', 'File could not be DELETED.'));

        return false;
    }
}
