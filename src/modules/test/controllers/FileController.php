<?php
/*
|-----------------------------------------------------------------
| Controller class for testing methods to work with files & directories
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\base\File;
use dezero\web\Controller;
use Yii;

class FileController extends Controller
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
        // Testing files
        $this->testFiles();

        // Testing directories
        $this->testDirectories();

        return $this->render('//test/test/index');
    }


    /**
     * Testing donwload feature via File class
     */
    public function actionDownload()
    {
        $file = File::load('@www/files/images/test.png');

        return $file->download();
    }


    /**
     * Testing FILES with class \dezero\base\File
     */
    private function testFiles()
    {
        // Testing with a FILE
        // '/Users/fabian/www/dezero.demo/www/files/images/test.png'
        // ------------------------------------------
        $file = File::load('@www/files/images/test.png');

        d("----------- TESTS WITH FILE '@www/files/images/test.png' -----------");

        d($file->realPath());
        d($file->pwd());
        d($file->exists());
        d($file->info());
        d($file->dirname());
        d($file->basename());
        d($file->extension());
        d($file->filename());
        d($file->isFile());
        d($file->isDirectory());
        d($file->isReadable());
        d($file->isWritable());
        d($file->mime());
        d($file->permissions());
        d($file->owner());
        d($file->group());
        d($file->size());
        d($file->formatSize());
        d($file->updatedDate());
        d($file->lastAccessDate());
        d($file->md5());
        d($file->isImage());

        d("----------- COPY TESTING -----------");

        // Copy a file
        $absolute_copied_file = $file->copy('@www/files/images/absolute_copy.png');
        $relative_copied_file = $file->copy('relative_copy.png');
        d($absolute_copied_file->realPath());
        d($relative_copied_file->realPath());

        d("----------- RENAME & DELETE TESTING -----------");

        // Move / rename a file
        $absolute_copied_file->rename('@www/files/images/moved_absolute_copy.png');
        $relative_copied_file->rename('moved_relative_copy.png');
        d($absolute_copied_file->realPath());
        d($relative_copied_file->realPath());

        // Deletes the copy
        d($absolute_copied_file->delete());
        d($relative_copied_file->delete());

        d("----------- ZIP TESTING -----------");

        // ZIP the file
        $zip_file = $file->zip();
        d($zip_file->realPath());
        d($zip_file->delete());

        d("----------- WRITE / APPEND / CLEAR NEW FILE -----------");

        // Write a new file
        $new_created_file = File::load('@www/files/images/test.txt');
        d($new_created_file->write('Some text goes here.'));
        d($new_created_file->read());
        d($new_created_file->append("\nMore content to add."));
        d($new_created_file->read());
        d($new_created_file->isEmpty());
        d($new_created_file->clear());
        d($new_created_file->isEmpty());
        d($new_created_file->read());

        d("----------- CHANGING PERMISSIONS -----------");

        // Set permissions
        d($new_created_file->setPermissions(664));
        d($new_created_file->permissions());

        d($new_created_file->delete());

        dd("----------- FINISHED TESTS -----------");
    }


    /**
     * Testing DIRECTORIES with class \dezero\base\File
     */
    private function testDirectories()
    {
        // Testing with a DIRECTORY
        // '/Users/fabian/www/dezero.demo/www/files/images'
        // ------------------------------------------
        $directory = File::load('@www/files/images');

        d("----------- TESTS WITH DIRECTORY '@www/files/images' -----------");

        d($directory->realPath());
        d($directory->pwd());
        d($directory->exists());
        d($directory->info());
        d($directory->dirname());
        d($directory->basename());
        d($directory->extension());
        d($directory->filename());
        d($directory->isFile());
        d($directory->isDirectory());
        d($directory->isReadable());
        d($directory->isWritable());
        d($directory->mime());
        d($directory->permissions());
        d($directory->owner());
        d($directory->group());
        d($directory->size());
        d($directory->formatSize());
        d($directory->updatedDate());
        d($directory->lastAccessDate());

        // Read the content list of a directory
        d($directory->read());

        d("----------- CREATE NEW DIRECTORY '@www/files/images/test' -----------");

        // Create new directory
        /// d(File::createDirectory('@www/files/image/test'));
        $new_directory = File::ensureDirectory('@www/files/images/test');
        d($new_directory->realPath());

        $file = File::load('@www/files/images/test.png');
        $new_copy = $file->copy('@www/files/images/test/copied_test.png');
        // dd($new_copy->download());
        d($new_copy);
        d($new_directory->read());

        d("----------- WORKING WITH DIRECTORY TO '@www/files/tests' -----------");

        // Copy the directory
        $copied_directory = $directory->copy('@www/files/tests');
        d($copied_directory->realPath());

        // Rename directory
        d($new_directory->rename('test_renamed'));
        d($new_directory->realPath());
        d($new_directory->isEmpty());
        d($new_directory->clear());
        d($new_directory->isEmpty());

        // Deletes directories
        d($copied_directory->delete());
        d($new_directory->delete());


        dd("----------- FINISHED TESTS -----------");
    }
}
