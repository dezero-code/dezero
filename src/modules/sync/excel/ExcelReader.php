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
use dezero\helpers\ArrayHelper;
use dezero\helpers\FileHelper;
use dezero\helpers\StringHelper;
use dezero\modules\sync\excel\ExcelHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Yii;
use yii\base\Exception;
use yii\di\Instance;

/**
 * Class to read Excel files
 */
class ExcelReader extends \yii\base\BaseObject
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
     * @var int
     */
    protected $sheet_number;


    /**
     * @var string
     */
    protected $sheet_name;


    /**
     * @var bool
     */
    protected $search_sheet_by_name;


    /**
     * @var array
     */
    private $vec_options;


    /*
    |--------------------------------------------------------------------------
    | CONSTRUCTOR METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Constructor
     */
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = Instance::ensure($spreadsheet, Spreadsheet::class);

        $this->init();
    }


    /**
     * Initializes the object
     */
    public function init() : void
    {
        // Default options
        $this->sheet_number = 1;
        $this->sheet_name = '';
        $this->search_sheet_by_name = false;

        $this->vec_options = [
            // Headers
            'is_header_row'         => true,
            'header_on_row'         => 0,
            'vec_custom_headers'    => [],

            // Type options
            'is_parse_string'       => true,
            'date_format'           => 'd/m/Y - H:i:s',     // 'U'

            // Limit && offset
            'rows_offset'           => null,
            'rows_limit'            => null,
            'columns_offset'        => null,
            'columns_limit'         => null,
        ];
    }


    /**
     * Named constructor to create a new Excel object given a file path
     */
    public static function load(string $file_path) : self
    {
        // Return the real file path from a Yii alias or normalize it
        $real_path = FileHelper::realPath($file_path);

        // Load Spreadsheet object
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($real_path);

        return new static($spreadsheet);
    }


    /**
     * Named constructor to create a new Excel object given a Spreadsheet object
     */
    public static function fromObject(Spreadsheet $spreadsheet) : self
    {
        return new static($spreadsheet);
    }


    /*
    |--------------------------------------------------------------------------
    | GETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Return Spreadsheet main object
     */
    public function getSpreadsheet() : Spreadsheet
    {
        return $this->spreadsheet;
    }


    /**
     * Return Spreadsheet main object
     */
    public function getWorksheet() : ?Worksheet
    {
        return $this->worksheet;
    }


    /**
     * Return the current offset of rows for the actived PhpSpreadsheet Sheet
     */
    public function getCurrentRow() : int
    {
        return $this->current_row;
    }


    /**
     * Return the current offset of columns for the actived PhpSpreadsheet Sheet
     */
    public function getCurrentColumn() : int
    {
        return $this->current_col;
    }


    /*
    |--------------------------------------------------------------------------
    | SETTER METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Set the offset of rows for the actived PhpSpreadsheet Sheet
     */
    public function setCurrentRow(int $offset_row = 0) : self
    {
        $this->current_row = (int)$offset_row;

        return $this;
    }


    /**
     * Set the offset of columns for the actived PhpSpreadsheet Sheet
     */
    public function setCurrentColumn(int $offset_col = 0) : self
    {
        $this->current_col = (int)$offset_col;

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | HEADER ROW OPTIONS METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Check if first row has been defined as header
     */
    public function isHeaderRow() : bool
    {
        return $this->vec_options['is_header_row'] ;
    }


    /**
     * Set the row number for the header
     */
    public function headerOnRow(int $header_row): self
    {
        $this->vec_options['header_on_row'] = $header_row;

        return $this;
    }


    /**
     * Disable header row
     */
    public function noHeaderRow(): self
    {
        $this->vec_options['is_header_row'] = false;

        return $this;

    }


    /**
     * Custom headers
     */
    public function customHeaders(array $vec_headers) : self
    {
        $this->vec_options['vec_custom_headers'] = $vec_headers;

        return $this;
    }


    /*
    |--------------------------------------------------------------------------
    | MORE OPTIONS METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Force all the values to be as string type (default)
     */
    public function enableParseStringValues() : self
    {
        $this->vec_options['is_parse_string'] = true;

        return $this;
    }


    /**
     * Don't force all the values to be as string type
     */
    public function disableParseStringValues() : self
    {
        $this->vec_options['is_parse_string'] = false;

        return $this;
    }


    /**
     * Check all the values must be parsed to string type
     */
    public function isParseStringValues() : bool
    {
        return $this->vec_options['is_parse_string'] ;
    }


    /**
     * Offset rows given as input parameter
     */
    public function offset(int $offset) : self
    {
        $this->vec_options['rows_offset'] = $offset;

        return $this;
    }


    /**
     * Limit the rows to be returned
     */
    public function limit(int $limit) : self
    {
        $this->vec_options['rows_limit'] = $limit;

        return $this;
    }


    /**
     * Offset columns given as input parameter
     */
    public function offsetColumns($offset) : self
    {
        if ( is_string($offset) )
        {
            $offset = ExcelHelper::alpha2num($offset);
            $offset = $offset > 0 ? $offset - 1 : $offset;
        }

        if ( is_int($offset) )
        {
            $this->vec_options['columns_offset'] = $offset;
        }

        return $this;
    }


    /**
     * Limit the columns to be returned
     */
    public function limitColumns(int $limit) : self
    {
        $this->vec_options['columns_limit'] = $limit;

        return $this;
    }


    /**
     * Defines a format for date and datetime values
     */
    public function setDateFormat(string $format) : self
    {
        $this->vec_options['date_format'] = $format;

        return $this;
    }



    /*
    |--------------------------------------------------------------------------
    | SHEET METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Get sheet count
     */
    public function getSheetCount() : int
    {
        return $this->spreadsheet->getSheetCount();
    }


    /**
     * Get active sheet index
     */
    public function getActiveSheetIndex() : int
    {
        return $this->spreadsheet->getActiveSheetIndex();
    }


    /**
     * Return all the sheet names
     */
    public function getSheetNames() : array
    {
        return $this->spreadsheet->getSheetNames();
    }


    /**
     * Return all the sheet objects
     */
    public function getAllSheets() : array
    {
        return $this->spreadsheet->getAllSheets();
    }


    /**
     * Reset cached PhpSpreadsheet sheet object and helper data
     */
    public function resetSheet() : void
    {
        $this->worksheet = null;
        $this->current_row = 0;
        $this->current_col = 0; // A1 => 1
    }


    /**
     * Set the active sheet given the number where '1' is the first sheet
     */
    public function fromSheet(int $sheet_number) : self
    {
        if ( $sheet_number > 0 )
        {
            $this->sheet_number = $sheet_number;
            $this->search_sheet_by_name = false;
        }

        return $this;
    }


    /**
     * Set the active sheet given the number where '0' is the first sheet
     */
    public function fromSheetName(string $sheet_name) : self
    {
        $this->sheet_name = $sheet_name;
        $this->search_sheet_by_name = true;

        return $this;
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

        $this->worksheet = $this->search_sheet_by_name ? $this->getActiveSheetByName() : $this->getActiveSheetByIndex();

        if ( $this->worksheet !== null )
        {
            return $this->worksheet;
        }

        // Active sheet does not exists
        throw new Exception("Invalid or empty sheet");
    }


    /**
     * Return the active sheet object by name
     */
    protected function getActiveSheetByName(): ?Worksheet
    {
        $worksheet = $this->spreadsheet->getSheetByName($this->sheet_name);
        if ( $worksheet !== null )
        {
            // Mark the current sheet as active
            return $this->spreadsheet->setActiveSheetIndex($this->spreadsheet->getIndex($worksheet));
        }

        return null;
    }


    /**
     * Return the active sheet object by index
     */
    protected function getActiveSheetByIndex(): ?Worksheet
    {
        return $this->spreadsheet->setActiveSheetIndex($this->sheet_number - 1);
        // return $this->spreadsheet->getSheet($this->sheet_number - 1);
    }


    /**
     * Set an active PhpSpreadsheet Sheet
     *
     * @param object|int $sheet PhpSpreadsheet sheet object or index number
     * @param string $title Sheet title
     * @param bool $is_normalize_title Auto-normalize title rule
     */
    /*
    public function setSheet($sheet = 0, ?string $title = null, bool $is_normalize_title = false) : self
    {
        $this->resetSheet();

        // Worksheet object given as input
        if ( is_object($sheet) && $sheet instanceof Worksheet )
        {
            $this->worksheet = &$sheet;
        }

        // Sheet index number given as input
        else if ( is_numeric($sheet) && $sheet >= 0 && $this->spreadsheet )
        {
            // Sheets Check
            $sheet_count = $this->getSheetCount();
            if ( $sheet >= $sheet_count )
            {
                for ( $i = $sheet_count; $i <= $sheet; $i++ )
                {
                    $this->spreadsheet->createSheet($i);
                }
            }

            // Select sheet
            $this->worksheet = $this->spreadsheet->setActiveSheetIndex($sheet);
        }

        // Auto create a sheet without index
        else if ( is_null($sheet) && $this->spreadsheet )
        {
            $this->setSheet($this->getSheetCount());
        }

        else
        {
            throw new Exception("Invalid or empty PhpSpreadsheet Object for setting sheet");
        }

        if ( $title === null )
        {
            return $this;
        }

        // Auto-normalize title rule
        if ( $is_normalize_title )
        {
            $title = ExcelHelper::normalizeTitle($title);
        }

        // Set title
        $this->worksheet->setTitle($title);

        return $this;
    }
    */


    /*
    |--------------------------------------------------------------------------
    | GET ROWS
    |--------------------------------------------------------------------------
    */

    /**
     * Get data of the next row from the actived sheet of PhpSpreadsheet
     *
     * @param callable $callback($cellValue, int $columnIndex, int $rowIndex)
     */
    public function nextRow(callable $callback = null) : array
    {
        $worksheet = $this->getSheet();

        // Calculate the column range of the worksheet
        $start_column = $this->vec_options['columns_offset'] !== null ? $this->vec_options['columns_offset'] : 0;
        $total_columns = $this->vec_options['columns_limit'] !== null ? ( $this->vec_options['columns_offset'] + $this->vec_options['columns_limit'] ) : ExcelHelper::alpha2num($worksheet->getHighestColumn());

        // Next row
        $this->current_row++;

        // Check if exceed highest row by PHPSpreadsheet highest row
        if ( $this->current_row > $worksheet->getHighestRow() )
        {
            return [];
        }

        // Fetch data from the sheet
        $vec_data = [];
        for ( $current_col = $start_column + 1; $current_col <= $total_columns; ++$current_col )
        {
            $cell = $worksheet->getCellByColumnAndRow($current_col, $this->current_row);
            $value = $cell->getValue();

            // Timestamp option
            if ( \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell) )
            {
                $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);

                // Timestamp Format option
                $value = $this->vec_options['date_format'] !== null ? date($this->vec_options['date_format'], $value) : $value;
            }

            // Parse value to string type?
            $value = $this->isParseStringValues() ? (string)$value : $value;

            // Callback function
            if ( $callback )
            {
                $callback($value, $current_col, $this->current_row);
            }

            $vec_data[] = $value;
        }

        return $vec_data;
    }


    /**
     * Return all the rows from the actived sheet of PhpSpreadsheet
     *
     * @param callable $callback($cellValue, int $columnIndex, int $rowIndex)
     */
    public function getRows(callable $callback = null) : array
    {
        $worksheet = $this->getSheet();

        // Get the highest row and column numbers referenced in the worksheet
        $highest_row = $worksheet->getHighestRow();
        $offset_row = ( $this->vec_options['rows_offset'] !== null && $this->vec_options['rows_offset'] <= $highest_row ) ? $this->vec_options['rows_offset'] : 0;
        $total_rows = ( $this->vec_options['rows_limit'] !== null && ($offset_row + $this->vec_options['rows_limit']) < $highest_row ) ? $this->vec_options['rows_limit'] : $highest_row - $offset_row;

        // Enhance performance for each nextRow()
        if ( $this->vec_options['columns_limit'] === null )
        {
            $this->limitColumns(ExcelHelper::alpha2num($worksheet->getHighestColumn()));
        }

        // Header row enabled?
        $vec_header_row = $this->getHeaderRow();

        // Set row offset
        $this->current_row = $offset_row;

        // Fetch data from the sheet
        $vec_data = [];
        for ( $i=1; $i <= $total_rows ; $i++ )
        {
            // Enabled header row?
            if ( $this->isHeaderRow() && $this->current_row === 0 )
            {
                // Next row
                $this->current_row++;

                // Exclude processing the first row
                continue;
            }

            $vec_row = $this->nextRow($callback);

            // Header rows as array keys
            if ( $this->isHeaderRow() && !empty($vec_header_row) )
            {
                $vec_row = array_combine($vec_header_row, $vec_row);
            }

            $vec_data[] = $vec_row;
        }

        return $vec_data;
    }


    /**
     * Generate the header row with the given options
     */
    public function getHeaderRow() : array
    {
        if ( ! $this->isHeaderRow() )
        {
            return [];
        }

        if ( !empty($this->vec_options['vec_custom_headers']) )
        {
            return $this->vec_options['vec_custom_headers'];
        }

        return $this->getRow($this->vec_options['header_on_row']);
    }


    /**
     * Return a row given the row index where '0' is the first
     */
    public function getRow(int $row_index) : array
    {
        $this->current_row = $row_index;

        return $this->nextRow();
    }
}
