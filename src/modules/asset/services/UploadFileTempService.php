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

class UploadFileTempService implements ServiceInterface
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
    public function __construct(EntityActiveRecord $reference_model, string $file_attribute, AssetFile $asset_file_model)
    {
        $this->reference_model = $reference_model;
        $this->file_attribute = $file_attribute;
        $this->asset_file_model = $asset_file_model;
    }


    /**
     * @return bool
     */
    public function run() : bool
    {
        // New uploaded file?
        if ( ! $this->isUploadFile() )
        {
            // Delete previous file?
            $this->deletePreviousFile();

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
     * Check if we need to delete previous file
     */
    private function deletePreviousFile()
    {
        // Return file_attribute value from $_POST
        $file_attribute = Yii::$app->request->postAttribute($this->reference_model, $this->file_attribute);
        if ( $file_attribute !== null && empty($file_attribute) )
        {
            $this->last_action = 'delete';
            $this->asset_file_model->delete();
        }
    }


    /**
     * Save upload file into a filesystem directory
     */
    private function saveUploadFile()
    {
        $destination_directory = Yii::$app->user->getTempDirectory();
        $this->savedPath = $destination_directory->filePath();
        $this->savedFilename = Transliteration::file($this->uploadedFile->baseName . '.' . $this->uploadedFile->extension);

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
        $this->savedFile = File::load($this->savedPath . $this->savedFilename);
        $this->asset_file_model->setAttributes([
            'file_name'             => Transliteration::file($this->savedFile->basename()),
            'file_path'             => $this->savedPath,
            'file_mime'             => $this->savedFile->mime(),
            'file_size'             => $this->savedFile->size(),
            'asset_type'            => $this->savedFile->isImage() ? AssetFile::ASSET_TYPE_IMAGE : AssetFile::ASSET_TYPE_DOCUMENT,
            'reference_entity_uuid' => null,
            'reference_entity_type' => $this->reference_model->getEntityType(),
        ]);

        // More attributes
        $this->asset_file_model->original_file_name = $this->asset_file_model->file_name;

        // Validate model's attributes
        if ( ! $this->asset_file_model->validate() )
        {
            return false;
        }

        // Save the model
        $this->asset_file_model->save(false);

        return true;
    }
}
