<?php
/**
 * Base Upload class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\entity\ActiveRecord as EntityActiveRecord;
use Yii;

/**
 * Upload is the base class to handle upload process
 */
class Upload extends \yii\base\BaseObject
{
    /**
     * @var string Destination path
     */
    private $destination_path;


    /**
     * @var bool Indicate if current upload file has been saved on a temporary destination
     */
    private $is_temp = true;



    /**
     * Constructor
     */
    public function __construct(EntityActiveRecord $model, string $attribute, array $vec_config = [])
    {
        $this->model = $model;
        $this->attribute = $attribute;

        // BaseObject::construct() must be called
        parent::__construct($vec_config);
    }


    /**
     * Set destination path
     */
    public function setDestinationPath(string $destination_path) : self
    {
        $this->destination_path = $destination_path;

        return $this;
    }


    /**
     * Return destination path
     */
    public function getDestinationPath() : string
    {
        return $destination_path;
    }


    /**
     * Set as upload file saved as TEMP
     */
    public function setTemp(boold $is_temp) : self
    {
        $this->is_temp = $is_temp;

        return $this;
    }


    /**
     * Check if upload file was saved as TEMP
     */
    public function isTemp() : bool
    {
        return $this->is_temp;
    }
}
