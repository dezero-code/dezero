<?php
/*
|-----------------------------------------------------------------
| Controller class for testing uploading
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\web\Controller;
use Dz;
use user\models\User;
use Yii;
use yii\web\UploadedFile;

class UploadController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action
     */
    public function actionIndex()
    {
        // Load User model
        $user_id = 1;
        $user_model = Dz::loadModel(User::class, $user_id);

        // Validate model via AJAX
        // $this->validateAjaxRequest($user_model);

        if ( Yii::$app->request->isPost )
        {
            // Avatar
            /*
            $user_model->avatarFile = UploadedFile::getInstance($user_model, 'avatarFile');
            if ( $user_model->uploadAvatar() )
            {
                // File is uploaded successfully
                Yii::$app->session->setFlash('success', Yii::t('user', 'Avatar uploaded succesfully'));
            }

            // Documents
            $user_model->documentFiles = UploadedFile::getInstances($user_model, 'documentFiles');
            if ( $user_model->uploadDocuments() )
            {
                // File is uploaded successfully
                Yii::$app->session->setFlash('success', Yii::t('user', 'Documents uploaded succesfully'));
            }
            */


            return $this->redirect(['/test/file']);
        }

        return $this->render('//test/file/upload', [
            'user_model'            => $user_model,
        ]);
    }
}
