<?php
/*
|--------------------------------------------------------------------------
| Use case "Save a single file"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\asset\services;

use Dz;
use dezero\base\File;
use dezero\contracts\ServiceInterface;
use dezero\db\ActiveRecord as ActiveRecord;
use dezero\entity\ActiveRecord as EntityActiveRecord;
use dezero\helpers\Log;
use dezero\helpers\Transliteration;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\entity\models\EntityFile;
use dezero\traits\ErrorTrait;
use yii\web\UploadedFile;
use Yii;

class FileSaveService implements ServiceInterface
{
    use ErrorTrait;


    /**
     * Constructor
     */
    public function __construct(ActiveRecord $reference_model, string $file_attribute, AssetFile $asset_file_model, ?EntityFile $entity_file_model)
    {
        $this->reference_model = $reference_model;
        $this->file_attribute = $file_attribute;
        $this->asset_file_model = $asset_file_model;
        $this->entity_file_model = $entity_file_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // Init process
        $this->init();

        // Save AssetFile model
        if ( ! $this->saveAssetFile() )
        {
            $this->addError('AssetFile model could not be created for new uploaded file');

            return false;
        }

        // Save EntityFile model
        if ( ! $this->saveEntityFile() )
        {
            $this->addError('AssetFile model could not be created for the new uploaded file');

            return false;
        }

        return true;
    }


    /**
     * Init process
     */
    private function init() : void
    {
    }


    /**
     * Save AssetFile model with optional entity reference
     */
    private function saveAssetFile()
    {
        // Save entity reference
        if ( $this->reference_model instanceof EntityActiveRecord )
        {
            $this->asset_file_model->reference_entity_uuid = ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : null;
            $this->asset_file_model->reference_entity_type = $this->reference_model->getEntityType();
        }

        // Validate model's attributes
        if ( ! $this->asset_file_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->asset_file_model->save(false);

        return true;
    }


    /**
     * Save EntityFile model
     */
    private function saveEntityFile() : bool
    {
        // Only save entity information for EntityActiveRecord objects
        if ( $this->entity_file_model === null || ! $this->reference_model instanceof EntityActiveRecord )
        {
            return true;
        }

        $this->entity_file_model->setAttributes([
            'file_id'           => $this->asset_file_model->file_id,
            'entity_uuid'       => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : 0,
            'entity_type'       => $this->reference_model->getEntityType(),
            'entity_source_id'  => $this->reference_model->getSourceId()
        ]);

        // Validate model's attributes
        if ( ! $this->entity_file_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->entity_file_model->save(false);

        return true;
    }
}
