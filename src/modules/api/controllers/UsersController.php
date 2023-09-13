<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\controllers;

use dezero\rest\Controller;
use Yii;

class UsersController extends Controller
{
    /**
     * List action for User models
     */
    public function actionTest()
    {
        // $vec_input = $this->jsonInput();

        return [
            'status_code'   => 100,
            'errors'        => ['Testing']
        ];
    }
}
