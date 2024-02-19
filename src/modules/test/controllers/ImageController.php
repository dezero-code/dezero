<?php
/*
|-----------------------------------------------------------------
| Controller class for testing methods to work with images
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\base\Image;
use dezero\web\Controller;
use Yii;

class ImageController extends Controller
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
        // Testing images
        $this->testImages();

        return $this->render('//test/test/index');
    }


    /**
     * Testing IMAGES with class \dezero\base\Image
     */
    private function testImages()
    {
        // Testing with a FILE
        // '/Users/fabian/www/dezero.demo/www/files/images/test.png'
        // ------------------------------------------

        d("----------- TESTS WITH FILE '@www/files/images/test.png' -----------");

        $image_png = Image::load('@www/files/images/test.png');
        d($image_png->realPath());
        d($image_png->getWidth());
        d($image_png->getHeight());

        d("----------- RESIZING to 200x100 -----------");
        $image_png->resize(200,100)->save('test-new.png');
        // $image_png->resizeFill(200,100)->save('test-new.png');
        // $image_png->resizeForce(200,100)->save('test-new.png');
        // $image_png->resizeCrop(200,100)->save('test-new.png');
        $resized_image = Image::load('@www/files/images/test-new.png');
        d($resized_image->realPath());
        d($resized_image->getWidth());
        d($resized_image->getHeight());

        d("----------- CHANGING IMAGE FORMAT -----------");
        $image_png->format('jpg')->save('test.jpg');
        $image_jpg = Image::load('@www/files/images/test.jpg');
        d($image_jpg->mime());
        d($image_jpg->realPath());
        d($image_jpg->getWidth());
        d($image_jpg->getHeight());
        d($image_jpg->mime());


        d("----------- OPTIMIZING IMAGE -----------");
        d($image_png->formatSize());
        $image_png->optimize()->save('test-optimized.png');
        $optimized_png = Image::load('@www/files/images/test-optimized.png');
        d($optimized_png->formatSize());

        d($image_jpg->formatSize());
        $image_jpg->optimize()->save('test-optimized.jpg');
        $optimized_jpg = Image::load('@www/files/images/test-optimized.png');
        d($optimized_jpg->formatSize());

        dd("----------- FINISHED TESTS -----------");
    }
}
