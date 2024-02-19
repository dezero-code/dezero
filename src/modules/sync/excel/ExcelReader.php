<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 *
 * @see https://github.com/PHPOffice/PhpSpreadsheet/tree/1.29.0
 * @see https://github.com/yidas/phpspreadsheet-helper
 */

namespace dezero\modules\sync\excel;

use Dz;
use dezero\helpers\ArrayHelper;
use dezero\helpers\FileHelper;
use dezero\helpers\StringHelper;
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
     * @var arry
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
        $this->vec_options = [
            'is_header_row'     => false,
            'is_parse_string'   => true,
            'rows_offset'       => null,
            'rows_limit'        => null,
            'columns_offset'    => null,
            'columns_limit'     => null,
            'date_format'       => 'd/m/Y - H:i:s',     // 'U'
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
    | OPTIONS METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Enable first row as header
     */
    public function enableHeaderRow() : self
    {
        $this->vec_options['is_header_row'] = true;

        return $this;
    }


    /**
     * Disable first row as header (default)
     */
    public function disableHeaderRow() : self
    {
        $this->vec_options['is_header_row'] = false;

        return $this;
    }


    /**
     * Check if first row has been defined as header
     */
    public function isHeaderRow() : bool
    {
        return $this->vec_options['is_header_row'] ;
    }


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
            $offset = $this->alpha2num($offset);
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
     * Get PhpSpreadsheet Sheet object
     *
     * @param int|string $identity Sheet index or name
     * @param bool $autoCreate
     * @return object PhpSpreadsheet Sheet object
     */
    public function getSheet($index_or_name = null, bool $is_auto_create = false) : Worksheet
    {
        // By default, return the active sheet
        if ( $index_or_name === null ) {

            return $this->worksheet;
        }

        if ( !is_numeric($index_or_name) && !is_string($index_or_name) )
        {
            return $this->worksheet;
        }


        // Given a sheet index number
        if ( is_numeric($index_or_name) )
        {
            $worksheet = $this->spreadsheet->getSheet($index_or_name);

            // Auto create if not exist
            if ( ! $worksheet && $is_auto_create )
            {
                // Create a new sheet by index
                $worksheet = $this->setSheet($index_or_name)->getSheet();
            }

            return $worksheet;
        }

        // Given a sheet name
        $worksheet = $this->spreadsheet->getSheetByName($index_or_name);

        // Auto create if not exist
        if ( ! $worksheet && $is_auto_create )
        {
            // Create a new sheet by name
            $worksheet = $this->setSheet(null, $index_or_name, true)->getSheet();
        }

        return $worksheet;
    }


    /**
     * Set an active PhpSpreadsheet Sheet
     *
     * @param object|int $sheet PhpSpreadsheet sheet object or index number
     * @param string $title Sheet title
     * @param bool $is_normalize_title Auto-normalize title rule
     */
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
            $title = $this->normalizeTitle($title);
        }

        // Set title
        $this->worksheet->setTitle($title);

        return $this;
    }


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
        $worksheet = $this->ensureWorksheet();

        // Calculate the column range of the worksheet
        $start_column = $this->vec_options['columns_offset'] !== null ? $this->vec_options['columns_offset'] : 0;
        $total_columns = $this->vec_options['columns_limit'] !== null ? ( $this->vec_options['columns_offset'] + $this->vec_options['columns_limit'] ) : $this->alpha2num($worksheet->getHighestColumn());

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
        $worksheet = $this->ensureWorksheet();

        // Get the highest row and column numbers referenced in the worksheet
        $highest_row = $worksheet->getHighestRow();
        $offset_row = ( $this->vec_options['rows_offset'] !== null && $this->vec_options['rows_offset'] <= $highest_row ) ? $this->vec_options['rows_offset'] : 0;
        $total_rows = ( $this->vec_options['rows_limit'] !== null && ($offset_row + $this->vec_options['rows_limit']) < $highest_row ) ? $this->vec_options['rows_limit'] : $highest_row - $offset_row;

        // Enhance performance for each nextRow()
        if ( $this->vec_options['columns_limit'] === null )
        {
            $this->limitColumns($this->alpha2num($worksheet->getHighestColumn()));
        }

        // Header row enabled?
        $vec_header_row = $this->isHeaderRow() ? $this->getFirstRow() : [];

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
     * Return the first row
     */
    public function getFirstRow() : array
    {
        $this->current_row = 0;

        return $this->nextRow();
    }



    /*
    |--------------------------------------------------------------------------
    | UTILITIES METHODS
    |--------------------------------------------------------------------------
    */

    /**
     * Alpha to Number
     *
     * Optimizing from \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString()
     *
     * @example A => 1, AA => 27
     *
     * @param int $n Excel column alpha
     */
    public function alpha2num(string $a) : int
    {
        $r = 0;
        $l = strlen($a);
        for ($i = 0; $i < $l; $i++)
        {
            $r += pow(26, $i) * (ord($a[$l - $i - 1]) - 0x40);
        }

        return (int)$r;
    }


    /**
     * Number to Alpha
     *
     * Optimizing from \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex()
     *
     * @example 1 => A, 27 => AA
     *
     * @param int $n column number
     */
    public static function num2alpha(int $n) : string
    {
        $n = $n - 1;
        $r = '';
        for ($i = 1; $n >= 0 && $i < 10; $i++)
        {
            $r = chr(0x41 + ($n % pow(26, $i) / pow(26, $i - 1))) . $r;
            $n -= pow(26, $i);
        }

        return (string)$r;
    }



    /**
     * Validate and return the selected PhpSpreadsheet Sheet Object
     */
    private function ensureWorksheet() : Worksheet
    {
        if ( $this->worksheet !== null && $this->worksheet instanceof Worksheet )
        {
            return $this->worksheet;
        }

        if ( $this->spreadsheet instanceof Spreadsheet )
        {
            // Set to default sheet if is unset
            return $this->setSheet()->getSheet();
        }

        throw new Exception("Invalid or empty PhpSpreadsheet Worksheet");
    }


    /**
     * Normalize title
     *
     * @see PhpOffice\PhpSpreadsheet\Worksheet\Worksheet
     */
    private function normalizeTitle(string $title) : string
    {
        // Invalid characters
        $vec_invalid_characters = ['*', ':', '/', '\\', '?', '[', ']'];

        // Some of the printable ASCII characters are invalid:  * : / \ ? [ ]
        $title = str_replace($vec_invalid_characters, '', $title);

        // Maximum 31 characters allowed for sheet title
        return StringHelper::substr($title, 0, 31);
    }
}
