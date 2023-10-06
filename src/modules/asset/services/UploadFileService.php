<?php
/*
|--------------------------------------------------------------------------
| Use case "Upload a file into TEMP directory"
|--------------------------------------------------------------------------
*/

namespace dezero\modules\asset\services;

use Dz;
use dezero\base\File;
use dezero\contracts\ServiceInterface;
use dezero\entity\ActiveRecord as EntityActiveRecord;
use dezero\helpers\Log;
use dezero\helpers\Transliteration;
use dezero\modules\asset\models\AssetFile;
use dezero\modules\entity\models\EntityFile;
use dezero\traits\ErrorTrait;
use yii\web\UploadedFile;
use Yii;

class UploadFileService implements ServiceInterface
{
    use ErrorTrait;

    /**
     * Register last action
     */
    public $last_action = 'upload';


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
     * @var string
     */
    private $savedFilename;


    /**
     * Constructor
     */
    public function __construct(EntityActiveRecord $reference_model, string $file_attribute, AssetFile $asset_file_model, EntityFile $entity_file_model, ?string $destination_path)
    {
        $this->reference_model = $reference_model;
        $this->file_attribute = $file_attribute;
        $this->asset_file_model = $asset_file_model;
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
            // Update or deleted previous file?
            $this->processPreviousFile();

            return false;
        }

        // Save UploadFile
        if ( ! $this->saveUploadFile() )
        {
            $this->addError('File could not be uploaded');

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
            $this->addError('AssetFile model could not be created for the new uploaded file');

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
        $this->uploadedFile = UploadedFile::getInstance($this->reference_model, $this->file_attribute);

        return $this->uploadedFile !== null && $this->uploadedFile instanceof UploadedFile;
    }


    /**
     * Update or deleted previous file
     */
    private function processPreviousFile()
    {
        // Return file_attribute value from $_POST
        $file_attribute = Yii::$app->request->postAttribute($this->reference_model, $this->file_attribute);
        if ( $file_attribute !== null )
        {
            // Delete previous uploaded file
            if ( empty($file_attribute) )
            {
                $this->last_action = 'delete';
                $this->asset_file_model->delete();
            }

            // Move file from TEMP directory to final destination
            else if  ( $this->asset_file_model->isTemp() )
            {
                $this->last_action = 'move';
                $this->asset_file_model->move($this->destination_path);
                $this->saveEntityFile();
            }
        }
    }


    /**
     * Save upload file into a filesystem directory
     */
    private function saveUploadFile()
    {
        $destination_directory = File::ensureDirectory($this->destination_path);
        $this->savedPath = $destination_directory->filePath();
        $this->savedFilename = Transliteration::file($this->uploadedFile->baseName .'.'. $this->uploadedFile->extension);

        // Check if it already exists a file with same name
        $existing_file = File::load($this->savedPath . $this->savedFilename);
        if ( $existing_file && $existing_file->exists() )
        {
            $this->savedFilename = Transliteration::file($this->uploadedFile->baseName .'_'. time() .'.'. $this->uploadedFile->extension);
        }

        return $this->uploadedFile->saveAs($this->savedPath . $this->savedFilename);
    }


    /**
     * Save AssetFile model
     */
    private function saveAssetFile()
    {
        // Replace a previous file?
        $old_file = null;
        if ( ! $this->asset_file_model->getIsNewRecord() && $this->asset_file_model->loadFile() )
        {
            $old_file = $this->asset_file_model->file;
        }

        $this->savedFile = File::load($this->savedPath . $this->savedFilename);
        $this->asset_file_model->setAttributes([
            'file_name'             => Transliteration::file($this->savedFile->basename()),
            'file_path'             => $this->savedPath,
            'file_mime'             => $this->savedFile->mime(),
            'file_size'             => $this->savedFile->size(),
            'asset_type'            => $this->savedFile->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
            'reference_entity_uuid' => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : null,
            'reference_entity_type' => $this->reference_model->getEntityType(),
        ]);

        // More attributes
        $this->asset_file_model->original_file_name = $this->asset_file_model->file_name;

        // Validate model's attributes
        if ( ! $this->asset_file_model->validate() )
        {
            return false;
        }

        // Remove replaced file?
        if ( $old_file !== null )
        {
            $old_file->delete();
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
