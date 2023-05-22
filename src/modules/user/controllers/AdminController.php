<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\controllers;

use dezero\modules\user\models\User;
use dezero\modules\user\models\search\UserSearch;
use Dz;
use yii\web\Controller;
use Yii;

class AdminController extends Controller
{
    /**
     * List action for User models
     */
    public function actionIndex()
    {
        $user_search_model = Dz::makeObject(UserSearch::class);

        $data_provider = $user_search_model->search(Yii::$app->request->get());
        $data_provider->pagination->pageSize=2;

        return $this->render('//user/admin/index',[
            'data_provider'     => $data_provider,
            'user_search_model' => $user_search_model
        ]);
    }
}
