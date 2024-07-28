<?php
/*
|-----------------------------------------------------------------
| Controller class for testing PDF generation
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\pdf\PdfBuilder;
use dezero\web\Controller;
use Yii;

class PdfController extends Controller
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
        $pdf_builder = PdfBuilder::create();
        $file_pdf = $pdf_builder
            ->setHtml('<html><h1>Test</h1></html>')
            // ->renderView('@theme/pdf/test', [
            //     'title' => 'Testing title',
            // ])
            ->download('test.pdf');

        return $this->render('//test/test/index');
    }
}
