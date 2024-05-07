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
        // Clear directory where backups are stored temporarily
        $this->clearTempDirectory();

        // Check directory exists
        $backups_path = Yii::getAlias('@backups/db');
        $backups_directory = File::load('@backups/db');
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
        $vec_files = $this->getBackupFiles($backups_directory->read());

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

        return $this->redirect(['/system/backup']);
    }


    /**
     * Delete a backup file
     */
    public function actionDelete(string $file)
    {
        // Delete action only allowed by POST requests
        $this->requirePostRequest();

        // Ensure file exists
        $backup_file = File::load(Yii::getAlias('@backups/db') . DIRECTORY_SEPARATOR . $file);
        if ( ! $backup_file || ! $backup_file->exists() )
        {
            throw new AssetNotFoundException("The backup file does not exist: {$file}");
        }

        // Delete file
        if ( $backup_file->delete() )
        {
            Yii::$app->session->setFlash('success', Yii::t('backend', 'Backup deleted successfully'));
        }
        else
        {
            Yii::$app->session->setFlash('error', Yii::t('backend', 'Backup deleted could not be deleted'));
        }

        return $this->redirect(['/system/backup']);
    }


    /**
     * Download a backup file
     */
    public function actionDownload(string $file)
    {
        // Delete action only allowed by POST requests
        $this->requirePostRequest();

        // Ensure file exists
        $backup_file = File::load(Yii::getAlias('@backups/db') . DIRECTORY_SEPARATOR . $file);
        if ( ! $backup_file || ! $backup_file->exists() )
        {
            throw new AssetNotFoundException("The backup file does not exist: {$file}");
        }

        // Copy SQL file from PRIVATE backup directory to PUBLIC temp directory
        $backup_temp_directory = File::ensureDirectory('@tmp/backups');
        $destination_file = $backup_file->copy($backup_temp_directory->realPath() . DIRECTORY_SEPARATOR . $file);
        if ( ! $destination_file || ! $destination_file->exists() )
        {
            throw new AssetNotFoundException("The backup file could not be downloaded: {$file}");
        }

        // Finally, download the file
        return $destination_file->download();
    }


    /**
     * Return backup files ordered by most recently updated
     */
    private function getBackupFiles(array $vec_files) : array
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

    /**
     * Clear directory where backups are stored temporarily
     */
    private function clearTempDirectory() : void
    {
        $temp_directory = File::load(Yii::getAlias('@tmp/backups'));
        if ( $temp_directory && $temp_directory->exists() )
        {
            $temp_directory->clear();
        }
    }
}
