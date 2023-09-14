<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\controllers;

use dezero\modules\api\resources\AssetsViewResource;
use dezero\rest\Controller;
use Dz;
use Yii;

class AssetsController extends Controller
{
    /**
     * Item action for AssetFile model: api/v1/assets/{id}
     */
    public function actionItem($id)
    {
        $assets_resource = Dz::makeObject(AssetsViewResource::class, [$id]);

        // #1 - VALIDATE INPUT PARAMS
        if ( $assets_resource->validate() )
        {
            // #2  - PROCESS REQUEST
            switch ( $assets_resource->method )
            {
                // View asset details - GET method: api/v1/assets/{id}
                case 'GET':
                    $assets_resource->run();
                break;
            }
        }

        return $assets_resource->sendResponse();
    }
}
