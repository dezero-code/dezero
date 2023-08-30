<?php
/*
|--------------------------------------------------------------------------
| Use case "Upload a file"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\asset\services;

use Dz;
use dezero\base\File;
use dezero\contracts\ServiceInterface;
use dezero\entity\ActiveRecord as EntityActiveRecord;
use dezero\helpers\Log;
use dezero\helpers\StringHelper;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\entity\models\EntityFile;
use dezero\traits\ErrorTrait;
use yii\helpers\Json;
use yii\web\UploadedFile;
use Yii;

class UploadFileService implements ServiceInterface
{
    use ErrorTrait;

    /**
     * @var \yii\web\UploadedFile
     */
    private $uploadedFile;


    /**
     * @var \dezero\base\File
     */
    private $savedFile;


    /**
     * @var string
     */
    private $savedPath;


    /**
     * Constructor
     */
    public function __construct(EntityActiveRecord $reference_model, AssetFile $asset_file_model, EntityFile $entity_file_model, ?string $destination_path)
    {
        $this->asset_file_model = $asset_file_model;
        $this->reference_model = $reference_model;
        $this->entity_file_model = $entity_file_model;
        $this->destination_path = $destination_path;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // New uploaded file?
        if ( ! $this->isUploadFile() )
        {
            return false;
        }

        // Save UploadFile
        if ( ! $this->saveUploadFile() )
        {
            $this->addError('Upload file could not be loaded');

            return false;
        }

        // Save AssetFile model
        if ( ! $this->saveAssetFile() )
        {
            $this->addError('AssetFile model could not be created for new uploaded file');

            return false;
        }


        // Save EntityFile model
        if ( ! $this->saveEntityFile() )
        {
            $this->addError('EntityFile model could not be created for new uploaded file');

            return false;
        }

        return true;
    }


    /**
     * Check if there is an uploaded file for the given model attribute.
     * Null is returned if no file is uploaded for the specified model attribute.
     */
    private function isUploadFile()
    {
        $this->uploadedFile = UploadedFile::getInstance($this->reference_model, $this->entity_file_model->relation_type);

        return $this->uploadedFile !== null && $this->uploadedFile instanceof UploadedFile;
    }


    /**
     * Save upload file into a filesystem directory
     */
    private function saveUploadFile()
    {
        $destination_directory = File::ensureDirectory($this->destination_path);
        $this->savedPath = $destination_directory->filePath() . DIRECTORY_SEPARATOR;

        // $temp_directory = Yii::$app->user->getTempDirectory();
        // $this->savedPath = $temp_directory->filePath() . DIRECTORY_SEPARATOR;

        return $this->uploadedFile->saveAs($this->savedPath . $this->uploadedFile->baseName . '.' . $this->uploadedFile->extension);
    }


    /**
     * Save AssetFile model
     */
    private function saveAssetFile()
    {
        $this->savedFile = File::load($this->savedPath . $this->uploadedFile->baseName . '.' . $this->uploadedFile->extension);
        $this->asset_file_model->setAttributes([
            'file_name'             => $this->savedFile->basename(),
            'file_path'             => $this->savedPath,
            'file_mime'             => $this->savedFile->mime(),
            'file_size'             => $this->savedFile->size(),
            'asset_type'            => $this->savedFile->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
            'reference_entity_uuid' => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : null,
            'reference_entity_type' => $this->reference_model->getEntityType(),
        ]);

        // More attributes
        $this->asset_file_model->original_file_name = $this->asset_file_model->file_name;
        $this->asset_file_model->file_options = Json::encode([
            'is_temp'           => 1,
            'destination_path'  => $this->destination_path
        ]);

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
    private function saveEntityFile()
    {
        $this->entity_file_model->setAttributes([
            'file_id'       => $this->asset_file_model->file_id,
            'entity_uuid'   => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : null,
            'entity_type'   => $this->reference_model->getEntityType(),
        ]);
        $reference_source_id = $this->reference_model->getSourceName();
        if ( is_numeric($reference_source_id) )
        {
            $this->entity_file_model->entity_source_id = $reference_source_id;
        }

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
