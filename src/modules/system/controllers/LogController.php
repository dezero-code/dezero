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

class LogController extends Controller
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
     * List action for User models
     */
    public function actionIndex()
    {
        $logs_path = Yii::getAlias('@logs');
        $logs_directory = File::load('@logs');

        // Check directory exists
        if ( ! $logs_directory->exists() || ! $logs_directory->isDirectory() )
        {
            throw new AssetNotFoundException("The logs directory does not exist: {$logs_path}");
        }

        // Check directory is readable
        if ( ! $logs_directory->isReadable() )
        {
            throw new AssetException("Directory is not readable: {$logs_path}");
        }

        // Get all files from log directory ordered by most recently updated
        $vec_files = $this->get_log_files($logs_directory->read());

        return $this->render('//system/log/index',[
            'vec_files'     => $vec_files
        ]);
    }


    /**
     * View the log file content
     */
    public function actionView($file)
    {
        // Check if log file exists
        $file_path = Yii::getAlias('@logs') . DIRECTORY_SEPARATOR . $file;
        $log_file = File::load($file_path);

        // Check log file exists
        if ( ! $log_file->exists() || ! $log_file->isFile() )
        {
            throw new AssetNotFoundException("The logs file does not exist: {$file_path}");
        }

        // Check log file is readable
        if ( ! $log_file->isReadable() )
        {
            throw new AssetException("Log file is not readable: {$file_path}");
        }

        // Parse log file
        $content_log = nl2br($log_file->read());
        $content_log = str_replace("\t", "&nbsp;", $content_log);
        $content_log = StringHelper::removeInvisibleCharacters($content_log);
        $encoded_log = Html::encode($content_log);
        if ( !empty($encoded_log) )
        {
            $content_log = $encoded_log;
        }
        $content_log = str_replace("&lt;br /&gt;", "<br>", $content_log);

        return $this->render('//system/log/view', [
            'log_file'      => $log_file,
            'content_log'   => $content_log
        ]);
    }


    /**
     * Return log files ordered by most recently updated
     */
    private function get_log_files(array $vec_files) : array
    {
        if ( empty($vec_files) )
        {
            return [];
        }

        $vec_log_files = [];
        $logs_path = Yii::getAlias('@logs');

        foreach ( $vec_files as $file_path )
        {
            // Accept only LOG files and exclude hidden files
            $file_name = StringHelper::trim($file_path);
            $file_name = str_replace($logs_path . DIRECTORY_SEPARATOR, '', $file_name);
            $file_name = str_replace($logs_path, '', $file_name);

            if ( preg_match("/\.log/", $file_name) && !preg_match("/^\./", $file_name) )
            {
                $file = File::load($file_path);
                if ( $file->exists() && $file->isFile() )
                {
                    $last_modified_time = $file->updatedDate();

                    // Avoid files updated exactly the same time
                    if ( isset($vec_log_files[$last_modified_time]) )
                    {
                        while ( isset($vec_log_files[$last_modified_time]) )
                        {
                            $last_modified_time++;
                        }
                    }
                    $vec_log_files[$last_modified_time] = $file;
                }
            }
        }

        if ( !empty($vec_log_files) )
        {
            krsort($vec_log_files);
        }

        return $vec_log_files;
    }

}
