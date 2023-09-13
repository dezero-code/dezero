<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\controllers;

use dezero\rest\Controller;
use dezero\modules\api\resources\UsersResource;
use Dz;
use Yii;

class UsersController extends Controller
{
    /**
     * List action for User models
     */
    public function actionIndex()
    {
        $user_resource = Dz::makeObject(UsersResource::class);

        // #1 - VALIDATE INPUT PARAMS
        if ( $users_resource->validate('index') )
        {
            // #2  - PROCESS REQUEST
            switch ( $users_resource->method )
            {
                // List of accepted users - GET method: api/v1/users
                case 'GET':
                    $users_resource->user_list();
                break;
            }
        }
        // $vec_input = $this->jsonInput();

        return [
            'status_code'   => 100,
            'errors'        => ['Testing']
        ];
    }
}
