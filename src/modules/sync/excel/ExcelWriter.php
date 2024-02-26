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
use PhpOffice\PhpSpreadsheet\Style\NumberFormat as CellFormat;
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
     * @var int
     */
    protected $sheet_number;


    /**
     * @var Spreadsheet
     */
    private $spreadsheet;


    /**
     * @var Worksheet
     */
    private $worksheet;


    /**
     * @var int
     */
    public $header_height;


    /**
     * @var int
     */
    public $row_height;


    /**
     * @var string
     */
    public $vertical_align;


    /**
     * @var bool
     */
    public $is_autosize;


    /**
     * @var bool
     */
    public $is_wrap_text;


    /**
     * @var array
     */
    public $vec_header_style;


    /**
     * @var array
     */
    public $vec_default_style;


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
        // Init properties
        $this->sheet_number = 1;
        $this->current_col = 0;
        $this->current_row = 0;

        // Default options
        $this->header_height = 20;
        $this->row_height = 20;
        $this->vertical_align = Alignment::VERTICAL_CENTER;
        $this->is_autosize = true;
        $this->is_wrap_text = true;

        // Default styles
        $this->vec_header_style = ['bold', 'font-size' => 14];
        $this->vec_default_style = ['font-size' => 13];
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
     * Add mutiple rows the active sheet
     */
    public function addRows(array $vec_rows) : self
    {
        foreach ( $vec_rows as $vec_row )
        {
            $this->addRow($vec_row);
        }

        return $this;
    }


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

        // Default height
        if ( $this->current_row === 0 )
        {
            $this->setHeaderHeight($this->header_height);
        }
        else
        {
            $this->setRowHeight($this->row_height);
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
            $vec_style = $this->vec_header_style;
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

        // Current column name
        $column_alpha = ExcelHelper::num2alpha($this->current_col);

        // Current coordinate. For example, "D6"
        $current_coordinate = $column_alpha . $this->current_row;

        // Set the value cell
        $this->worksheet->setCellValueByColumnAndRow($this->current_col, $this->current_row, $vec_cell['value']);

        // Cell width?
        if ( isset($vec_cell['width']) && !empty($vec_cell['width']) )
        {
            $this->worksheet->getColumnDimension($column_alpha)->setWidth($vec_cell['width']);
        }

        // Style
        $vec_style = ( isset($vec_cell['style']) && is_array($vec_cell['style']) ) ? $vec_cell['style'] : $this->vec_default_style;
        $vec_style = $this->customStyles($vec_style);
        $this->worksheet->getStyle($current_coordinate)->applyFromArray($vec_style);

        // Format code
        $format_code = isset($vec_cell['format']) ? $this->getFormatCode($vec_cell['format']) : CellFormat::FORMAT_TEXT;
        $this->worksheet->getStyle($current_coordinate)->getNumberFormat()->setFormatCode($format_code);
    }


    /*
    |--------------------------------------------------------------------------
    | SHEET METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return the active Worksheet object
     */
    public function getSheet() : Worksheet
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


    /**
     * Add a new sheet and set it as active
     */
    public function addSheet(string $sheet_title) : self
    {
        // Sheet title
        $sheet_title = ExcelHelper::normalizeTitle($sheet_title);

        // Check if this sheet's name already exists
        if ( $this->spreadsheet->sheetNameExists($sheet_title) )
        {
            return $this;
        }

        // Create new sheet
        $new_worksheet = $this->spreadsheet->createSheet();
        $new_worksheet->setTitle($sheet_title);

        // Select this new sheet as active
        $new_sheet_number = $this->spreadsheet->getIndex($new_worksheet) + 1;
        $this->setActiveSheet($new_sheet_number);

        return $this;
    }


    /**
     * Get the current active sheet index
     */
    public function getActiveSheetIndex() : int
    {
        return $this->spreadsheet->getActiveSheetIndex();
    }


    /**
     * Return the active sheet object by index
     */
    public function getActiveSheetByIndex(): ?Worksheet
    {
        return $this->spreadsheet->setActiveSheetIndex($this->sheet_number - 1);
    }


    /**
     * Set an active sheet given a number
     */
    public function setActiveSheet(int $sheet_number): self
    {
        if ( $sheet_number < 1 )
        {
            return $this;
        }

        $this->sheet_number = $sheet_number;
        $this->worksheet = $this->spreadsheet->setActiveSheetIndex($sheet_number - 1);

        return $this;
    }


    /**
     * Set a title for the active sheet
     */
    public function setActiveSheetTitle(string $sheet_title) : self
    {
        $this->worksheet->setTitle($sheet_title);

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | STYLE & OPTION METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Set the default style for all the cells
     */
    public function setDefaultStyle(array $vec_style) : self
    {
        $this->vec_default_style = $vec_style;

        return $this;
    }


    /**
     * Set the style of the header row
     */
    public function setHeaderStyle(array $vec_style) : self
    {
        return $this->setRowStyle($vec_style, 1);
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
    public function setVerticalAlign(?string $value = null, $range = null) : self
    {
        $vec_allowed_values = [
            Alignment::VERTICAL_BOTTOM,        // bottom
            Alignment::VERTICAL_TOP,           // top
            Alignment::VERTICAL_CENTER,        // center
            Alignment::VERTICAL_JUSTIFY,       // justify
            Alignment::VERTICAL_DISTRIBUTED,   // distributed --> Excel2007 only
        ];

        // Default value
        if ( $value === null || ! in_array($value, $vec_allowed_values, false) )
        {
            $value = $this->vertical_align;
        }

        // Use full dimension as default
        if ( $range === null )
        {
            $range = $this->getWorksheetDimension();
            $this->vertical_align = $value;
        }

        $this->worksheet
            ->getStyle($range)
            ->getAlignment()
            ->setVertical($value);

        return $this;
    }


    /**
     * Set wrap text option
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


    /**
     * Disable the wrap text option (enabled by default)
     */
    public function noWrapText() : self
    {
        $this->is_wrap_text = false;

        return $this;
    }


    /**
     * Set auto width (auto size)
     */
    public function setAutoSize(?string $start_column = null, ?string $end_column = null) : self
    {
        $start_column = $start_column !== null ? ExcelHelper::alpha2num($start_column) : 1;
        $end_column = $end_column !== null ? ExcelHelper::alpha2num($end_column) : ExcelHelper::alpha2num($this->worksheet->getHighestColumn());

        for ( $current_col = $start_column; $current_col <= $end_column; ++$current_col )
        {
            $this->worksheet->getColumnDimension(ExcelHelper::num2alpha($current_col))->setAutoSize(true);
        }

        return $this;
    }


    /**
     * Disable the auto size or auto with option (enabled by default)
     */
    public function noAutoSize() : self
    {
        $this->is_autosize = false;

        return $this;
    }


    /**
     * Set a row's height
     */
    public function setRowHeight(int $height, ?int $row_number = null) : self
    {
        // Get number of the row to apply the style
        $row_number = $row_number !== null ? $row_number : $this->current_row;

        $this->worksheet->getRowDimension($row_number)->setRowHeight($height);

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
    public function setRowStyle(array $vec_style, ?int $row_number = null) : self
    {
        // Get number of the row to apply the style
        $row_number = $row_number !== null ? $row_number : $this->current_row;

        // Get last column in ALPHA
        $last_column = $this->worksheet->getHighestColumn();

        // Parse custom styles and apply them
        $vec_style = $this->customStyles($vec_style);
        $this->worksheet->getStyle("A{$row_number}:{$last_column}{$row_number}")->applyFromArray($vec_style);

        return $this;
    }


    /**
     * Parse from custom styles to PhpSpreadhSheet styles
     *
     * @see PhpOffice\PhpSpreadsheet\Style\Style::applyFromArray()
     *
     * @see https://phpspreadsheet.readthedocs.io/en/latest/topics/recipes/#valid-array-keys-for-style-applyfromarray
     */
    private function customStyles(array $vec_style) : array
    {
        if ( empty($vec_style) )
        {
            return [];
        }

        // Ensure default style groups exist
        $vec_style['alignment'] = $vec_style['alignment'] ?? [];
        $vec_style['borders'] = $vec_style['borders'] ?? [];
        $vec_style['fill'] = $vec_style['fill'] ?? [];
        $vec_style['font'] = $vec_style['font'] ?? [];

        foreach ( $vec_style as $style_name => $style_value )
        {
            // BOLD - Convert from ['bold'] to ['font' => ['bold' => true]]
            if ( $style_value === 'bold' )
            {
                $vec_style['font']['bold'] = true;
                unset($vec_style[$style_name]);
            }

            // ITALIC - Convert from ['italic'] to ['font' => ['italic' => true]]
            else if ( $style_value === 'italic' )
            {
                $vec_style['font']['italic'] = true;
                unset($vec_style[$style_name]);
            }

            // UNDERLINE - Convert from ['underline'] to ['font' => ['underline' => 'single']]
            //
            // ---> More values int \PhpOffice\PhpSpreadsheet\Style\Font.php
            //
            else if ( $style_value === 'underline' )
            {
                $vec_style['font']['underline'] = 'single';
                unset($vec_style[$style_name]);
            }

            // UNDERLINE - Convert from ['underline' => value] to ['font' => ['underline' => value]]
            //
            // ---> More values int \PhpOffice\PhpSpreadsheet\Style\Font.php
            //
            else if ( $style_name === 'underline' && !empty($style_value) )
            {
                $vec_style['font']['underline'] = $style_value;
                unset($vec_style[$style_name]);
            }

            // FONT SIZE - Convert from ['font-size' => 16] to ['font' => ['size' => 16]]
            else if ( ( $style_name === 'font-size' || $style_name === 'size' ) && !empty($style_value) )
            {
                $vec_style['font']['size'] = $style_value;
                unset($vec_style[$style_name]);
            }

            // COLOR - Convert from ['color' => '#808080'] to ['font' => ['color' => ['rgb' => '808080']]]
            else if ( $style_name === 'color' && !empty($style_value) )
            {
                $vec_style['font']['color'] = ['rgb' => str_replace('#', '', $style_value)];
                unset($vec_style[$style_name]);
            }

            // ALIGNMENT HORIZONTAL - Convert from ['align' => 'center'] to ['alignment' => ['horizontal' => 'center']]
            else if ( ( $style_name === 'align' || $style_name === 'alignment' || $style_name === 'horizontal' || $style_name === 'text-align' ) && !empty($style_value) )
            {
                $vec_style['alignment']['horizontal'] = $style_value;
                unset($vec_style[$style_name]);
            }

            // ALIGNMENT VERTICAL - Convert from ['vertical' => 'center'] to ['alignment' => ['vertical' => 'center']]
            else if ( ( $style_name === 'vertical-align' || $style_name === 'vertical' ) && !empty($style_value) )
            {
                $vec_style['alignment']['vertical'] = $style_value;
                unset($vec_style[$style_name]);
            }

            // WRAP TEXT - Convert from ['wrap-text'] to ['alignment' => ['wrapText' => true]]
            else if ( $style_value === 'wrap' || $style_value === 'wrap-text' )
            {
                $vec_style['alignment']['wrapText'] = true;
                unset($vec_style[$style_name]);
            }

            // BACKGROUND COLOR - Convert from ['fill' => '#808080'] to ['fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '808080']]]
            else if ( ( $style_name === 'fill' || $style_name === 'background' ) && !empty($style_value) )
            {
                $vec_style['fill']['fillType'] = 'solid';
                $vec_style['fill']['startColor'] = ['rgb' => str_replace('#', '', $style_value)];
                unset($vec_style[$style_name]);
            }

            // BORDERS - Convert from ['border-bottom'] to ['borders' => ['bottom' => 'borderStyle' => 'medium']]
            //
            // ---> More values int \PhpOffice\PhpSpreadsheet\Style\Border.php
            //
            else if ( $style_value === 'border-top' || $style_value === 'border-bottom' || $style_value === 'border-left' || $style_value === 'border-right' )
            {
                $border_position = str_replace('border-', '', $style_value);
                $vec_style['borders'][$border_position] = ['borderStyle' => 'thin'];
                unset($vec_style[$style_name]);
            }
            else if ( $style_value === 'borders' )
            {
                $vec_style['borders']['outline'] = ['borderStyle' => 'thin'];
                unset($vec_style[$style_name]);
            }
        }

        return $vec_style;
    }


    /**
     * Parse from custom FORMATS to PhpSpreadhSheet FORMATS
     *
     * @see PhpOffice\PhpSpreadsheet\Style\NumberFormat
     */
    private function getFormatCode(string $format_name) : string
    {
        // DATE (dd/mm/yyyy)
        if ( $format_name === 'date' )
        {
            return CellFormat::FORMAT_DATE_DDMMYYYY;
        }

        // DATE (dd/mm/yyyy - h:mm)
        else if ( $format_name === 'datetime' )
        {
            return 'dd/mm/yyy hh:mm';
        }

        // CURRENCY (125,12 €)
        else if ( $format_name === 'currency' )
        {
            return CellFormat::FORMAT_CURRENCY_EUR;
        }

        // PERCENTAGE (0.00%)
        else if ( $format_name === 'percentage' )
        {
            return CellFormat::FORMAT_PERCENTAGE_00;
        }

        // INTEGER (no decimals)
        else if ( $format_name === 'integer' )
        {
            return CellFormat::FORMAT_NUMBER;
        }

        // FLOAT (with decimals)
        else if ( $format_name === 'number' || $format_name === 'float' )
        {
            return CellFormat::FORMAT_NUMBER_00;
        }

        return $format_name;
    }

    /*
    |--------------------------------------------------------------------------
    | FREEZE METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Freeze the header row (fixed)
     *
     * Alias of freezeFirstRow() method
     */
    public function freezeHeader() : self
    {
        return $this->freezeRows(1);
    }


    /**
     * Freeze the first row
     */
    public function freezeFirstRow() : self
    {
        return $this->freezeRows(1);
    }


    /**
     * Freeze a given number of rows
     */
    public function freezeRows(int $rows_number) : self
    {
        $rows_number = $rows_number + 1;

        $this->worksheet->freezePane("A{$rows_number}");

        return $this;
    }


    /**
     * Freeze the first column
     */
    public function freezeFirstColumn() : self
    {
        return $this->freezeColumns(1);
    }


    /**
     * Freeze a given number of columns
     */
    public function freezeColumns(int $cols_number) : self
    {
        $column_alpha = ExcelHelper::num2alpha($cols_number + 1);

        $this->worksheet->freezePane("{$column_alpha}1");

        return $this;
    }


    /**
     * Freeze the header row and a given number of columns
     */
    public function freezeHeaderAndColumns(int $columns_num) : self
    {
        $column_alpha = ExcelHelper::num2alpha($columns_num + 1);

        $this->worksheet->freezePane("{$column_alpha}2");

        return $this;
    }


    /**
     * Freeze from a specific cell
     */
    public function freezeCell(string $cell_coordinate) : self
    {
        $this->worksheet->freezePane($cell_coordinate);

        return $this;
    }



    /*
    |--------------------------------------------------------------------------
    | LOCK METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Lock current sheet
     */
    public function lockSheet() : self
    {
        $this->worksheet->getProtection()->setSheet(true);

        return $this;
    }


    /**
     * Unlock current sheet
     */
    public function unlockSheet() : self
    {
        $this->worksheet->getProtection()->setSheet(false);

        return $this;
    }


    /**
     * Lock or protect a column
     */
    public function lockColumn(string $column_alpha) : self
    {
        $last_row = $this->worksheet->getHighestRow();
        $column_dimension = "{$column_alpha}1:{$column_alpha}{$last_row}";

        $this->lockCell($column_dimension);

        return $this;
    }


    /**
     * Lock or protect a column
     */
    public function unlockColumn(string $column_alpha) : self
    {
        $last_row = $this->worksheet->getHighestRow();
        $column_dimension = "{$column_alpha}1:{$column_alpha}{$last_row}";

        $this->unlockCell($column_dimension);

        return $this;
    }


    /**
     * Lock or protect a row
     */
    public function lockRow(int $row_number) : self
    {
        // Get last column in ALPHA
        $last_column = $this->worksheet->getHighestColumn();
        $row_dimension = "A{$row_number}:{$last_column}{$row_number}";

        $this->lockCell($row_dimension);

        return $this;
    }


    /**
     * Unlock or unprotect a row
     */
    public function unlockRow(int $row_number) : self
    {
        // Get last column in ALPHA
        $last_column = $this->worksheet->getHighestColumn();
        $row_dimension = "A{$row_number}:{$last_column}{$row_number}";

        $this->unlockCell($row_dimension);

        return $this;
    }


    /**
     * Lock / protect a cell
     */
    public function lockCell(string $cell_coordinate) : self
    {
        $this->worksheet->getStyle($cell_coordinate)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_PROTECTED);

        return $this;
    }


    /**
     * Unlock / unprotect a cell
     */
    public function unlockCell(string $cell_coordinate) : self
    {
        $this->worksheet->getStyle($cell_coordinate)->getProtection()->setLocked(\PhpOffice\PhpSpreadsheet\Style\Protection::PROTECTION_UNPROTECTED);

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | DOCUMENT PROPERTIES METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Set document creator
     */
    public function setCreator(string $creator) : self
    {
        $this->spreadsheet->getProperties()->setCreator($creator);

        return $this;
    }

    /**
     * Set document title
     */
    public function setTitle(string $title) : self
    {
        $this->spreadsheet->getProperties()->setTitle($title);

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | CUSTOM EVENT METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Custom event before saving the Excel file
     */
    public function beforeSave()
    {
        // Vertical align?
        if ( $this->vertical_align !== null )
        {
            $this->setVerticalAlign($this->vertical_align);
        }

        // Auto size?
        if ( $this->is_autosize === true )
        {
            $this->setAutoSize();
        }

        // Wrap text?
        if ( $this->is_wrap_text === true )
        {
            $this->setWrapText();
        }
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
        // Trigger custom event "beforeSave"
        $this->beforeSave();

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
