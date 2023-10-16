<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\controllers;

use dezero\modules\asset\models\AssetFile;
use dezero\web\Controller;
use yii\web\NotFoundHttpException;

class DownloadController extends Controller
{
    /**
     * Private download action
     */
    public function actionIndex($uuid)
    {
        // Load Asset File model
        $asset_file_model = AssetFile::find()->uuid($uuid)->one();
        if ( ! $asset_file_model || ! $asset_file_model->loadFile() )
        {
            throw new NotFoundHttpException("File not found: $uuid");
        }

        return $asset_file_model->file->download();
    }
}
