<?php
/*
|--------------------------------------------------------------------------
| Use case "Upload multiple files"
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
use dezero\traits\WarningTrait;
use yii\web\UploadedFile;
use Yii;

class UploadMultipleFileService implements ServiceInterface
{
    use ErrorTrait;
    use WarningTrait;


    /**
     * @var array of \yii\web\UploadedFile
     */
    private $vec_uploaded_files;


    /**
     * @var array
     */
    private $vec_saved_filenames;


    /**
     * @var string
     */
    private $saved_path;


    /**
     * @var \dezero\modules\asset\models\AssetFile
     */
    private $asset_file_model;


    /**
     * Constructor
     */
    public function __construct(EntityActiveRecord $reference_model, string $name, string $destination_path)
    {
        $this->reference_model = $reference_model;
        $this->name = $name;
        $this->destination_path = $destination_path;

        $this->vec_uploaded_files = [];
        $this->vec_saved_filenames = [];
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // New uploaded files?
        if ( ! $this->isUploadFiles() )
        {
            return false;
        }

        // Save UploadFile
        if ( ! $this->saveUploadFiles() )
        {
            return false;
        }

        // Save AssetFile models
        if ( ! $this->saveAssetFiles() )
        {
            return false;
        }

        return true;
    }


    /**
     * Check if there are uploaded files for the given name
     */
    private function isUploadFiles() : bool
    {
        $this->vec_uploaded_files = UploadedFile::getInstancesByName($this->name);

        return is_array($this->vec_uploaded_files) && !empty($this->vec_uploaded_files) && $this->vec_uploaded_files[0] instanceof UploadedFile;
    }


    /**
     * Save uploaded files into a filesystem directory
     */
    private function saveUploadFiles() : bool
    {
        $destination_directory = File::ensureDirectory($this->destination_path);
        $this->saved_path = $destination_directory->filePath();

        foreach ( $this->vec_uploaded_files as $num_file => $uploaded_file )
        {
            $saved_filename = Transliteration::file($uploaded_file->baseName .'.'. $uploaded_file->extension);

            // Check if it already exists a file with same name
            $existing_file = File::load($this->saved_path . $saved_filename);
            if ( $existing_file && $existing_file->exists() )
            {
                $saved_filename = Transliteration::file($uploaded_file->baseName .'_'. time() .'.'. $uploaded_file->extension);
            }

            // Save file
            if ( ! $uploaded_file->saveAs($this->saved_path . $saved_filename) )
            {
                $this->addWarning("File #{$num_file} - {$saved_filename} cannot be uploaded");
            }
            else
            {
                // Save filename
                $this->vec_saved_filenames[$num_file] = $saved_filename;
            }
        }

        return true;
    }


    /**
     * Save AssetFile models
     */
    private function saveAssetFiles() : bool
    {
        foreach ( $this->vec_saved_filenames as $num_file => $saved_filename )
        {
            $savedFile = File::load($this->saved_path . $saved_filename);

            $this->asset_file_model = Dz::makeObject(AssetFile::class);
            $this->asset_file_model->setAttributes([
                'file_name'             => Transliteration::file($savedFile->basename()),
                'file_path'             => $this->saved_path,
                'file_mime'             => $savedFile->mime(),
                'file_size'             => $savedFile->size(),
                'asset_type'            => $savedFile->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
                'reference_entity_uuid' => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : null,
                'reference_entity_type' => $this->reference_model->getEntityType(),
            ]);

            // More attributes
            $this->asset_file_model->original_file_name = $this->asset_file_model->file_name;

            // Validate model's attributes
            if ( ! $this->asset_file_model->validate() )
            {
                Log::saveModelError($this->asset_file_model);
                $this->addWarning("File #{$num_file} - {$this->asset_file_model->file_name} AssetFile could not be created");
            }
            else
            {
                // Save the model
                $this->asset_file_model->save(false);

                if ( ! $this->saveEntityFile() )
                {
                    $this->addWarning("File #{$num_file} - {$this->asset_file_model->file_name} EntityFile could not be created");
                }
            }
        }

        return true;
    }


    /**
     * Save EntityFile model
     */
    private function saveEntityFile() : bool
    {
        $entity_file_model = Dz::makeObject(EntityFile::class);
        $entity_file_model->setAttributes([
            'file_id'           => $this->asset_file_model->file_id,
            'entity_uuid'       => ! $this->reference_model->getIsNewRecord() && $this->reference_model->hasAttribute('entity_uuid') ? $this->reference_model->getAttribute('entity_uuid') : 0,
            'entity_type'       => $this->reference_model->getEntityType(),
            'entity_source_id'  => $this->reference_model->getSourceId(),
            'relation_type'     => $this->name
        ]);

        // Validate model's attributes
        if ( ! $entity_file_model->validate() )
        {
            Log::saveModelError($entity_file_model);

            return false;
        }

        // Save the model
        $entity_file_model->save(false);

        return true;
    }
}
