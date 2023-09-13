<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\controllers;

use dezero\rest\Controller;
use dezero\modules\api\resources\AuthResource;
use Dz;
use Yii;

class AuthController extends Controller
{
    /**
     * Get an authorization token
     */
    public function actionToken()
    {
        $auth_resource = Dz::makeObject(AuthResource::class);

        // #1 - VALIDATE INPUT PARAMS
        if ( $auth_resource->validate() )
        {
            // #2  - PROCESS REQUEST
            switch ( $auth_resource->method )
            {
                // Authorization token - POST method: api/v1/auth/token
                case 'POST':
                    $auth_resource->run();
                break;
            }
        }

        // #3 - SEND RESPONSE
        return $auth_resource->sendResponse();
    }
}
