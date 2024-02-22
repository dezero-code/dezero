<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 *
 * @see https://github.com/PHPOffice/PhpSpreadsheet/tree/1.29.0
 * @see https://github.com/yidas/phpspreadsheet-helper
 * @see https://github.com/spatie/simple-excel
 */

namespace dezero\modules\sync\excel;

use Dz;
use dezero\base\File;
use dezero\helpers\ArrayHelper;
use dezero\helpers\FileHelper;
use dezero\helpers\StringHelper;
use dezero\modules\sync\excel\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yii;
use yii\base\Exception;
use yii\di\Instance;

/**
 * Class to create Excel files
 */
class ExcelWriter extends \yii\base\BaseObject
{
    /**
     * @var int Current column offset for the actived sheet
     */
    private $current_col;


    /**
     * @var int Current row offset for the actived sheet
     */
    private $current_row;


    /**
     * @var Spreadsheet
     */
    private $spreadsheet;


    /**
     * @var Worksheet
     */
    private $worksheet;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->spreadsheet = Dz::makeObject(Spreadsheet::class);

        $this->init();
    }

    /**
     * Initializes the object
     */
    public function init() : void
    {
        $this->current_col = 0;
        $this->current_row = 0;
    }


    /**
     * Named constructor to create an empty new Excel object
     */
    public static function create() : self
    {
        return new static();
    }



    /*
    |--------------------------------------------------------------------------
    | WRITE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Add a row to the active sheet
     */
    public function addRow(array $vec_row, $vec_style = null) : self
    {
        $worksheet = $this->getSheet();

        // Reset column
        $this->current_col = 0;

        // Next row
        $this->current_row++;

        foreach ( $vec_row as $num_cell => $vec_cell )
        {
            if ( !is_array($vec_cell) )
            {
                $vec_cell = [
                    'value' => $vec_cell
                ];
            }

            // Add & process a new cell
            $this->addCell($vec_cell);
        }

        // Set style for the full row
        if ( $vec_style !== null && is_array($vec_style) )
        {
            return $this->setRowStyle($vec_style);
        }

        return $this;
    }


    /**
     * Add the header row to the active sheet
     */
    public function addHeader(array $vec_row, $vec_style = null) : self
    {
        $this->current_row = 0;

        if ( empty($vec_style) )
        {
            // Bold
            $vec_style = [
                'font' => [
                    'bold' => true
                ]
            ];
        }

        return $this->addRow($vec_row, $vec_style);
    }



    /**
     * Add & process a new cell
     */
    public function addCell(array $vec_cell) : void
    {
        // Next column
        $this->current_col++;

        // Set the value cell
        $this->worksheet->setCellValueByColumnAndRow($this->current_col, $this->current_row, $vec_cell['value']);
        /*
        $vec_attributes = [
            'key' => null,
            'value' => null,
            'col' => 1,
            'row' => 1,
            'skip' => 1,
            'width' => null,
            'style' => null,
        ];
        */
    }


    /*
    |--------------------------------------------------------------------------
    | SHEET METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get active sheet index
     */
    public function getActiveSheetIndex() : int
    {
        return $this->spreadsheet->getActiveSheetIndex();
    }

    /**
     * Return the active sheet object by index
     */
    protected function getActiveSheetByIndex(): ?Worksheet
    {
        return $this->spreadsheet->setActiveSheetIndex(0);
    }


    /**
     * Return the active PhpSpreadsheet Sheet object
     */
    protected function getSheet() : Worksheet
    {
        // First of all, check the cached property
        if ( $this->worksheet !== null && $this->worksheet instanceof Worksheet )
        {
            return $this->worksheet;
        }

        $this->worksheet = $this->getActiveSheetByIndex();

        if ( $this->worksheet !== null )
        {
            return $this->worksheet;
        }

        // Active sheet does not exists
        throw new Exception("Invalid or empty sheet");
    }


    /*
    |--------------------------------------------------------------------------
    | STYLE & OPTION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Set a row's height
     */
    public function setRowHeight(int $height, ?int $num_row = null) : self
    {
        // Get number of the row to apply the style
        $num_row = $num_row !== null ? $num_row : $this->current_row;

        $this->worksheet->getRowDimension($num_row)->setRowHeight($height);

        return $this;
    }


    /**
     * Set the height of the header row
     */
    public function setHeaderHeight(int $height) : self
    {
        return $this->setRowHeight($height, 1);
    }


    /**
     * Set style for a full row
     */
    public function setRowStyle(array $vec_style, ?int $num_row = null) : self
    {
        // Get number of the row to apply the style
        $num_row = $num_row !== null ? $num_row : $this->current_row;

        // Get last column in ALPHA
        $last_column = $this->worksheet->getHighestColumn();

        $this->worksheet->getStyle("A{$num_row}:{$last_column}{$num_row}")->applyFromArray($vec_style);

        return $this;
    }


    /**
     * Return the dimension of the active worksheet. Example: "A1:K29"
     */
    public function getWorksheetDimension() : string
    {
        return $this->worksheet->calculateWorksheetDimension();
    }


    /**
     * Set vertical align for all thecells or by giving a range
     */
    public function setVerticalAlign(string $value, $range = null) : self
    {
        $vec_allowed_values = [
            Alignment::VERTICAL_BOTTOM,        // bottom
            Alignment::VERTICAL_TOP,           // top
            Alignment::VERTICAL_CENTER,        // center
            Alignment::VERTICAL_JUSTIFY,       // justify
            Alignment::VERTICAL_DISTRIBUTED,   // distributed --> Excel2007 only
        ];

        // Center as default
        if ( ! in_array($value, $vec_allowed_values, false) )
        {
            $value = PhpOffice\PhpSpreadsheet\Style::VERTICAL_CENTER;
        }

        // Use full dimension as default
        if ( $range === null )
        {
            $range = $this->getWorksheetDimension();
        }

        $this->worksheet
            ->getStyle($range)
            ->getAlignment()
            ->setVertical($value);

        return $this;
    }


    /**
     * Set WrapText for all thecells or by giving a range
     */
    public function setWrapText(bool $value = true, $range = null) : self
    {
        // Use full dimension as default
        if ( $range === null )
        {
            $range = $this->getWorksheetDimension();
        }

        $this->worksheet
            ->getStyle($range)
            ->getAlignment()
            ->setWrapText($value);

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | OUTPUT METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Save the Excel file into a destination path
     */
    public function saveTo(string $file_path, string $format = null ) : File
    {
        // XLSX as default format
        if ( $format === null )
        {
            $format = \PhpOffice\PhpSpreadsheet\IOFactory::READER_XLSX;
        }

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($this->spreadsheet, $format);
        $writer->save($file_path);

        return File::load($file_path);
    }


    /**
     * Download the generated Excel
     */
    public function download(string $file_name = null, string $format = null) : void
    {
        // Generate a default file name
        if ( $file_name === null )
        {
            $now = time();
            $file_name = "excel-{$now}.xlsx";
        }

        // Save to the private TEMP directory
        $output_path = Yii::getAlias('@privateTmp') . DIRECTORY_SEPARATOR . $file_name;
        $excel_file = $this->saveTo($output_path);

        if ( $excel_file === null )
        {
            throw new Exception("Excel could not be generated");
        }

        // Finally, download the file (send to the browser)
        if ( $excel_file->download() === null )
        {
            throw new Exception("Excel could not be downloaded");
        }

        Yii::$app->end();
    }
}
