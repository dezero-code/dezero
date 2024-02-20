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

        // Get all rows with headers
        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');
        d($excel_reader->getRows());

        // Get all rows without headers and without parsing to string type
        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');
        d($excel_reader
            ->noHeaderRow()
            ->disableParseStringValues()
            ->getRows());

        // Get from sheet by index
        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');
        d($excel_reader
            ->fromSheet(2)
            ->getRows());

        // Get from sheet by name
        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');
        d($excel_reader
            ->fromSheetName('Line Items')
            ->getRows());

        d("----------- TOTAL SHEETS -----------");
        $excel_reader = ExcelReader::load('@www/files/tmp/export_orders.xls');
        d($excel_reader->getSheetNames());
        // d($excel_reader->getAllSheets());
        d($excel_reader->getSheetCount());
        d($excel_reader->getActiveSheetIndex());


        dd("----------- TESTS WITH FILE '@www/files/tmp/export_inscriptions.xls' -----------");

        $excel_reader = ExcelReader::load('@www/files/tmp/export_inscriptions.xls');

        // Get all rows
        d($excel_reader
            ->enableHeaderRow()
            ->getRows()
        );

        // Offset & limit rows
        d($excel_reader
            ->enableHeaderRow()
            ->offset(6)
            ->limit(10)
            ->getRows()
        );

        // Offset & limit columns by INDEX
        $excel_reader = ExcelReader::load('@www/files/tmp/export_inscriptions.xls');
        d($excel_reader
            ->enableHeaderRow()
            ->offsetColumns(4)
            ->limitColumns(5)
            ->getRows()
        );

        // Offset & limit columns by NAME
        $excel_reader = ExcelReader::load('@www/files/tmp/export_inscriptions.xls');
        d($excel_reader
            ->enableHeaderRow()
            ->offsetColumns('E')
            ->limitColumns(5)
            ->setDateFormat('U')    // Unix format
            ->getRows()
        );


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
