<?php
/*
|-----------------------------------------------------------------
| Controller class for testing Excel classes
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\modules\sync\excel\ExcelReader;
use dezero\web\Controller;
use Yii;

class ExcelController extends Controller
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
        // Testing read an Excel
        $this->testReader();

        return $this->render('//test/test/index');
    }


    /**
     * Testing ExcelReader class
     */
    private function testReader()
    {
        d("----------- TESTS WITH FILE '@www/files/tmp/export_orders.xls' -----------");

        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');

        // Get all rows
        d($excel_reader->getRows());
        d($excel_reader->getSpreadsheet());

        d("----------- TOTAL SHEETS -----------");

        d($excel_reader->getSheetNames());
        // d($excel_reader->getAllSheets());
        d($excel_reader->getSheetCount());
        d($excel_reader->getActiveSheetIndex());



        d("----------- SHEET #0 - ORDERS -----------");

        // Get sheet by index
        d($excel_reader->getSheet(0)->getCodeName());
        d($excel_reader->getSheet(0)->getTitle());

        // Get sheet by name
        d($excel_reader->getSheet('Orders')->getTitle());


        d("----------- SHEET #1 - LINE ITEMS -----------");

        // Get sheet by index
        d($excel_reader->getSheet(1)->getCodeName());
        d($excel_reader->getSheet(1)->getTitle());

        // Get sheet by name
        d($excel_reader->getSheet('Line Items')->getTitle());


        d("----------- GET ROWS SHEET #1 - LINE ITEMS -----------");

        // Set sheet
        $excel_reader->setSheet(1);
        d($excel_reader->getRows());


        dd("----------- FINISHED TESTS -----------");
    }
}
