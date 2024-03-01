<?php
/*
|-----------------------------------------------------------------
| Controller class for testing Excel classes
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\modules\sync\excel\ExcelReader;
use dezero\modules\sync\excel\ExcelWriter;
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
        // Testing read from an Excel
        // $this->testReader();

        // Testing write to an Excel
        // $this->testWriterStyles();
        $this->testWriterSheets();

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


    /**
     * Testing ExcelWriter class with styles
     */
    private function testWriterStyles()
    {
        $excel_writer = ExcelWriter::create()
            ->addHeader(['#', 'FIRST', 'Second', 'ThIrD', 'f o u r t h', 'fIfTh'])
            ->setHeaderHeight(80)
            ->setHeaderStyle([
                'bold',
                'italic',
                'underline',
                'wrap',
                'font-size' => 20,
                'align' => 'center',
                'vertical-align' => 'center',
                'color' => '#FFFFFF',
                'background' => '#000000',
            ])

            // Override default style
            ->setDefaultStyle([
                'font-size' => 14,
                'align' => 'right'
            ])

            // ----------- FREEZE METHODS -----------
            // ->freezeHeader()
            // ->freezeFirstRow()
            // ->freezeRows(2)
            // ->freezeFirstColumn()
            // ->freezeColumns(2)
            ->freezeHeaderAndColumns(1)
            // ->freezeCell('C3')

            // ----------- SIMPLE ROWS -----------
            ->addRows([
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #1', 'hola', 'esto', 'es', 'una', 'prueba'],
                ['Fila #2', 'segunda', 'prueba', 'que', 'hacemos', 'ahora'],
                ['Fila #3', 'hola', 'esto', 'es', 'una', 'prueba'],
            ])

            // ----------- ROW STYLE -----------
            ->setRowStyle([
                'font-size' => 14,
                'borders'
            ], 3)
            ->setRowStyle([
                'font-size' => 8,
                'border-bottom'
            ], 4)

            // ----------- DOCUMENT PROPERTIES -----------
            ->setCreator('Dezero')
            ->setTitle('Testing Excel genetation for Dezero Framework')

            // ----------- DEFAULT PROPERTIES -----------
            // ->setAutoSize()
            // ->setWrapText()
            // ->setVerticalAlign('center')


            ->download();
    }


    /**
     * Testing ExcelWriter class with mutiple sheets & formats
     */
    private function testWriterSheets()
    {
        $excel_writer = ExcelWriter::create()

            // Sheet #1
            ->addHeader(['Name', 'Lastname'])
            ->setHeaderHeight(30)
            ->setActiveSheetTitle('Nombres & apellidos')
            ->addHeader(['Nombre', 'Apellidos'])
            ->addRows([
                ['Enjuto', 'Mojamuto'],
                ['Hincli', 'Mincli']
            ])
            ->addDropdown('A2', ['Enjuto', 'Hicli', 'Otro', 'Vamos'])
            ->addDropdown('A3', '__data__!$A$2:$A$4')

            // Sheet #2 - FORMATS
            ->addSheet('[Fechas*')   // This title must be replaced from "[Fechas*" to "Fechas"
            ->setHeaderHeight(30)
            ->addHeader(['Nombre', 'Fecha Nacimiento', 'Edad', 'PVP', 'Porcentaje', 'Peso'])
            ->addRows([
                [
                    [
                        'value'     => 'Enjuto',
                        'style'     => ['bold', 'font-size' => 18],
                        'width'     => 20,
                        'filter'    => ['Enjuto', 'Enjuto 2', 'Enjuto 3', 'Otro valor']
                    ],
                    [
                        'value' => '10/01/1985',
                        'format' => 'date',
                        'width' => 30
                    ],
                    [
                        'value' => 35,
                        'format' => 'integer'
                    ],
                    [
                        'value' => 28.45,
                        'format' => 'currency'
                    ],
                    [
                        'value' => 0.8205,
                        'format' => 'percentage'
                    ],
                    [
                        'value' => 65.6,
                        'format' => 'number'
                    ]
                ],

                [
                    [
                        'value' => 'Hincli',
                        'style' => ['italic', 'font-size' => 16],
                        'width' => 20,
                        'filter' => '__data__!$A$2:$A$4'
                    ],
                    [
                        'value' => '20/12/2001',
                        'format' => 'date',
                        'width' => 30
                    ],
                    [
                        'value' => 35,
                        'format' => 'integer'
                    ],
                    [
                        'value' => 28.45,
                        'format' => 'currency'
                    ],
                    [
                        'value' => 0.2205,
                        'format' => 'percentage'
                    ],
                    [
                        'value' => 65.6,
                        'format' => 'number'
                    ]
                ],
            ])

            // LAST ROW with Formulas
            ->addRow([
                [
                    'value' => '=COUNT(A1:A2)',
                    'format' => 'integer'
                ],
                '',
                [
                    'value' => '=C2+C3',
                    'format' => 'integer'
                ],
                [
                    'value' => '=SUM(D2:D3)',
                    'format' => 'currency'
                ],
                [
                    'value' => '=AVERAGE(E2:E3)',
                    'format' => 'percentage'
                ],
                ''
            ])
            ->setRowHeight(30)
            ->setRowStyle(['italic', 'font-size' => 16, 'background' => '#FFFF00'])

            // Sheet #3 - DATA
            ->addSheet('__data__')
            ->addHeader(['Nombre'])
            ->addRows([
                ['Enjuto'],
                ['Hincli'],
                ['Mincli'],
                ['Otro']
            ])

            ->noAutoSize()
            ->setCreator('Dezero')
            ->setTitle('Testing Excel genetation for Dezero Framework')
            ->download();
    }
}
