<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\system\controllers;

use dezero\base\File;
use dezero\helpers\Html;
use dezero\helpers\StringHelper;
use dezero\errors\AssetException;
use dezero\errors\AssetNotFoundException;
use dezero\web\Controller;
use Dz;
use Yii;


/**
 * Controller class to create database backups
 */
class BackupController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Superadmin permissions are required
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * View all the backups
     */
    public function actionIndex()
    {
        $backups_path = Yii::getAlias('@backups/db');
        $backups_directory = File::load('@backups/db');

        // Check directory exists
        if ( ! $backups_directory->exists() || ! $backups_directory->isDirectory() )
        {
            throw new AssetNotFoundException("The backups directory does not exist: {$backups_path}");
        }

        // Check directory is readable
        if ( ! $backups_directory->isReadable() )
        {
            throw new AssetException("Directory is not readable: {$backups_path}");
        }

        // Get all files from backup directory ordered by most recently updated
        $vec_files = $this->get_backup_files($backups_directory->read());

        return $this->render('//system/backup/index',[
            'vec_files'     => $vec_files
        ]);
    }


    /**
     * Perform a backup operation
     */
    public function actionCreate()
    {
        $file_path = Yii::$app->db->backup(true);

        Yii::$app->session->setFlash('success', Yii::t('backend', 'Database backup generated successfully in <em>{file_path}</em>', ['file_path' => $file_path]));

        return $this->redirect(['index']);
    }


    /**
     * Return backup files ordered by most recently updated
     */
    private function get_backup_files(array $vec_files) : array
    {
        if ( empty($vec_files) )
        {
            return [];
        }

        $vec_backup_files = [];
        $backups_path = Yii::getAlias('@backups/db');

        foreach ( $vec_files as $file_path )
        {
            // Accept only SQL files and exclude hidden files
            $file_name = StringHelper::trim($file_path);
            $file_name = str_replace($backups_path . DIRECTORY_SEPARATOR, '', $file_name);
            $file_name = str_replace($backups_path, '', $file_name);

            if ( preg_match("/\.sql/", $file_name) && !preg_match("/^\./", $file_name) )
            {
                $file = File::load($file_path);
                if ( $file->exists() && $file->isFile() )
                {
                    $last_modified_time = $file->updatedDate();

                    // Avoid files updated exactly the same time
                    if ( isset($vec_backup_files[$last_modified_time]) )
                    {
                        while ( isset($vec_backup_files[$last_modified_time]) )
                        {
                            $last_modified_time++;
                        }
                    }
                    $vec_backup_files[$last_modified_time] = $file;
                }
            }
        }

        if ( !empty($vec_backup_files) )
        {
            krsort($vec_backup_files);
        }

        return $vec_backup_files;
    }
}
