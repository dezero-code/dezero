<?php
/**
 * UploadBehavior class file
 *
 * It saves uploaded file into the filesystem and create/updata the AssetFile model
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://dezero.es/
 * @copyright Copyright &copy; 2023 Dezero
 *
 * @see https://github.com/mohorev/yii2-upload-behavior
 */
namespace dezero\behaviors;

use dezero\base\File;
use dezero\helpers\StringHelper;
use dezero\helpers\Transliteration;
use Yii;
use yii\base\Behavior;
use yii\base\InvalidConfigException;
use yii\behaviors\AttributeBehavior;
use yii\db\BaseActiveRecord;
use yii\db\Expression;
use yii\web\UploadedFile;

class UploadBehavior extends AttributeBehavior
{
    /**
     * @var string the attribute which holds the attachment.
     */
    public $attribute;


    /**
     * @var bool Getting file instance by name
     */
    public $instanceByName = false;


    /**
     * @var string the base path or path alias to the directory in which to save files.
     */
    public $path;


    /**
     * @var array the scenarios in which the behavior will be triggered
     */
    public $scenarios = [];


    /**
     * @var boolean If `true` current attribute file will be deleted
     */
    public $unlinkOnSave = true;


    /**
     * @var UploadedFile the uploaded file instance.
     */
    private $file;


    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if ( $this->attribute === null )
        {
            throw new InvalidConfigException('The "attribute" property must be set.');
        }

        if ( $this->path === null )
        {
            throw new InvalidConfigException('The "path" property must be set.');
        }
    }


    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_BEFORE_VALIDATE => 'beforeValidate',
            BaseActiveRecord::EVENT_BEFORE_INSERT => 'beforeSave',
            BaseActiveRecord::EVENT_BEFORE_UPDATE => 'beforeSave',
            BaseActiveRecord::EVENT_AFTER_INSERT => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'afterSave',
            BaseActiveRecord::EVENT_AFTER_DELETE => 'afterDelete',
        ];
    }


    /**
     * This method is invoked before validation starts.
     */
    public function beforeValidate()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if ( in_array($model->scenario, $this->scenarios) )
        {
            if ( ($file = $model->getAttribute($this->attribute) ) instanceof UploadedFile)
            {
                $this->file = $file;
            }
            else
            {
                if ( $this->instanceByName === true )
                {
                    $this->file = UploadedFile::getInstanceByName($this->attribute);
                }
                else
                {
                    $this->file = UploadedFile::getInstance($model, $this->attribute);
                }
            }
            if ( $this->file instanceof UploadedFile )
            {
                $this->file->name = $this->getFileName($this->file);
                $model->setAttribute($this->attribute, $this->file);
            }
        }
    }


    /**
     * This method is called at the beginning of inserting or updating a record.
     */
    public function beforeSave()
    {
        /** @var BaseActiveRecord $model */
        $model = $this->owner;
        if ( in_array($model->scenario, $this->scenarios) )
        {
            if ( $this->file instanceof UploadedFile )
            {
                if ( !$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute) )
                {
                    if ( $this->unlinkOnSave === true )
                    {
                        $this->delete($this->attribute, true);
                    }
                }
                $model->setAttribute($this->attribute, $this->file->name);
            }
            else
            {
                // Protect attribute
                unset($model->{$this->attribute});
            }
        }
        else
        {
            if ( !$model->getIsNewRecord() && $model->isAttributeChanged($this->attribute) )
            {
                if ( $this->unlinkOnSave === true )
                {
                    $this->delete($this->attribute, true);
                }
            }
        }
    }


    /**
     * This method is called at the end of inserting or updating a record.
     * @throws \yii\base\InvalidArgumentException
     */
    public function afterSave()
    {
        if ( $this->file instanceof UploadedFile )
        {
            dd($this->file);
            /*
            $path = $this->getUploadPath($this->attribute);
            if ( is_string($path) && File::ensureDirectory(dirname($path)) )
            {
                $this->save($this->file, $path);
                $this->afterUpload();
            }
            else
            {
                throw new InvalidArgumentException(
                    "Directory specified in 'path' attribute doesn't exist or cannot be created."
                );
            }
            */
        }
    }


    /**
     * This method is invoked after deleting a record.
     */
    public function afterDelete()
    {
        $attribute = $this->attribute;
        if ( $this->unlinkOnDelete && $attribute )
        {
            $this->delete($attribute);
        }
    }


    /**
     * Deletes old file
     */
    private function delete(string $attribute, bool $old = false) : void
    {
        /*
        $path = $this->getUploadPath($attribute, $old);
        if (is_file($path)) {
            unlink($path);
        }
        */
    }



    /**
     * Get a sanitaze filename
     */
    private function getFileName(UploadedFile $file) : string
    {
        return Transliteration::file($file->name);
    }
}
