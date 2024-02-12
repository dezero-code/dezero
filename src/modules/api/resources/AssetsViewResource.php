<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\resources;

use dezero\helpers\RestHelper;
use dezero\modules\asset\models\AssetFile;
use dezero\rest\Resource;
use Yii;

class AssetsViewResource extends Resource
{
    /**
     * ID
     */
    private $id;


    /**
     * \dezero\modules\asset\models\AssetFile
     */
    private $asset_model;


    /**
     * Constructor
     */
    public function __construct(int $id, string $api_name = 'default', array $vec_config = [])
    {
        $this->id = $id;

        parent::__construct($api_name, $vec_config);
    }


    /**
     * Validate input parameters
     */
    public function validate() : bool
    {
        // Check auth validation from parent
        if ( ! parent::validate() )
        {
            return false;
        }

        // Validate if AssetFile model exists and file can be loaded
        if ( ! $this->validateModel() )
        {
            return false;
        }

        return true;
    }


    /**
     * Run the resource
     */
    public function run() : void
    {
        $this->vec_response = [
            'status_code'   => 1,
            'errors'        => [],
            'data'          => $this->getItemResponse()
        ];
    }


    /**
     * Validate if AssetFile model exists and file can be loaded
     */
    private function validateModel()
    {
        // Check if AssetFile model exists
        $this->asset_model = AssetFile::findOne($this->id);
        if ( ! $this->asset_model )
        {
            $this->addError(Yii::t('backend', 'Asset does not exist with id #{id}', [
                'id'    => $this->id
            ]));

            return false;
        }

        // Check if file exists in filesystem
        if ( ! $this->asset_model->loadFile() )
        {
            $this->addError(Yii::t('backend', 'File cannot be loaded for id #{id}', [
                'id'    => $this->id
            ]));

            return false;
        }

        return true;
    }


    /**
     * Get the output from an item model for REST API
     */
    private function getItemResponse()
    {
        return [
            'id'            => $this->asset_model->file_id,
            'file_name'     => $this->asset_model->file_name,
            'file_path'     => $this->asset_model->file_path,
            'file_mime'     => $this->asset_model->file_mime,
            'file_size'     => $this->asset_model->file_size,
            'file_content'  => $this->asset_model->file->base64_encode(),
            'created_date'  => RestHelper::formatDate($this->asset_model->created_date),
            'updated_date'  => RestHelper::formatDate($this->asset_model->updated_date)
        ];
    }
}
