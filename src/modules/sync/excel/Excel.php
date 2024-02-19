<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 *
 * @see https://github.com/PHPOffice/PhpSpreadsheet/tree/1.29.0
 * @see https://github.com/yidas/phpspreadsheet-helper
 */

namespace sync\excel;

use Dz;
use dezero\helpers\ArrayHelper;
use dezero\helpers\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;
use Yii;
use yii\base\Exception;
use yii\di\Instance;

/**
 * Base class to work with Excel files
 */
class Excel extends \yii\base\BaseObject
{
    /**
     * @var int Current column offset for the actived sheet
     */
    private $offset_col;


    /**
     * @var int Current row offset for the actived sheet
     */
    private $offset_row;


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
    public function __construct(Spreadsheet $spreadsheet)
    {
        $this->spreadsheet = Instance::ensure($this->spreadsheet, Spreadsheet::class);
    }


    /**
     * Named constructor to create an empty new Excel object
     */
    public static function create() : self
    {
        $spreadsheet = Dz::makeObject(Spreadsheet::class);

        return new static($spreadsheet);
    }


    /**
     * Named constructor to create a new Excel object given a file path
     */
    public static function fromPath(string $file_path) : self
    {
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);

        return new static($spreadsheet);
    }


    /**
     * Named constructor to create a new Excel object given a Spreadsheet object
     */
    public static function fromObject(Spreadsheet $spreadsheet) : self
    {
        return new static($spreadsheet);
    }


    /**
     * Get sheet count
     */
    public function getSheetCount() : int
    {
        return $this->spreadsheet ? $this->spreadsheet->getSheetCount() : 0;
    }


    /**
     * Get active sheet index
     */
    public function getActiveSheetIndex() : int
    {
        return $this->spreadsheet ? $this->spreadsheet->getActiveSheetIndex() : '';
    }


    /**
     * Set the offset of rows for the actived PhpSpreadsheet Sheet
     */
    public function setRowOffset(int $offset = 0) : self
    {
        $this->offset_row = (int)$offset;

        return $this;
    }


     /**
     * Set the offset of columns for the actived PhpSpreadsheet Sheet
     */
    public static function setColumnOffset(int $offset = 0) : self
    {
        $this->offset_col = (int)$offset;

        return $this;
    }


    /**
     * Reset cached PhpSpreadsheet sheet object and helper data
     */
    public function resetSheet() : void
    {
        $this->worksheet = null;
        $this->offset_row = 0;
        $this->offset_col = 0; // A1 => 1

        // return new static();
        // return $this;
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
     * Get data of a row from the actived sheet of PhpSpreadsheet
     *
     * @param bool $is_parse_string All values from sheet to be string type
     *
     * @param bool $vec_options [
     *  row (int) Ended row number
     *  column (int) Ended column number
     *  timestamp (bool) Excel datetime to Unixtime
     *  timestampFormat (string) Format for date() when usgin timestamp
     *  ]
     *
     * @param callable $callback($cellValue, int $columnIndex, int $rowIndex)
     *
     * @return array Data of Spreadsheet
     */
    public function getRow(bool $is_parse_string = true, array $vec_options = [], callable $callback = null) : array
    {
        $worksheet = $this->ensureWorksheet();

        // Options
        $vec_default_options = [
            'columnOffset' => 0,
            'columns' => null,
            'timestamp' => true,
            'timestampFormat' => 'Y-m-d H:i:s', // False would use Unixtime
        ];

        $vec_options = !empty($vec_options) ? ArrayHelper::merge($vec_default_options, $vec_options) : $vec_default_options;

        // Calculate the column range of the worksheet
        $start_column = ( $vec_options['columnOffset'] ) ?: 0;
        $total_columns = ($vec_options['columns']) ?: $this->alpha2num($worksheet->getHighestColumn());

        // Next row
        $this->$offset_row++;

        // Check if exceed highest row by PHPSpreadsheet highest row
        if ( $this->$offset_row > $worksheet->getHighestRow() )
        {
            return [];
        }

        // Fetch data from the sheet
        $vec_data = [];
        for ( $col = $start_column + 1; $col <= $total_columns; ++$col )
        {
            $cell = $worksheet->getCellByColumnAndRow($col, $this->offset_row);
            $value = $cell->getValue();

            // Timestamp option
            if ( $vec_options['timestamp'] && \PhpOffice\PhpSpreadsheet\Shared\Date::isDateTime($cell) )
            {
                $value = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($value);

                // Timestamp Format option
                $value = ($vec_options['timestampFormat']) ? date($vec_options['timestampFormat'], $value) : $value;
            }

            $value = ($is_parse_string) ? (string)$value : $value;

            // Callback function
            if ( $callback )
            {
                $callback($value, $col, $this->offset_row);
            }
            $vec_data[] = $value;
        }

        return $vec_data;
    }


    /**
     * Get rows from the actived sheet of PhpSpreadsheet
     *
     * @param bool $is_parse_string All values from sheet to be string type
     *
     * @param bool $vec_options [
     *  row (int) Ended row number
     *  column (int) Ended column number
     *  timestamp (bool) Excel datetime to Unixtime
     *  timestampFormat (string) Format for date() when usgin timestamp
     *  ]
     *
     * @param callable $callback($cellValue, int $columnIndex, int $rowIndex)
     *
     * @return array Data of Spreadsheet
     */
    public function getRows(bool $is_parse_string = true, array $vec_options = [], callable $callback=null) : array
    {
        $worksheet = $this->ensureWorksheet();

        // Options
        $vec_default_options = [
            'rowOffset'         => 0,
            'rows'              => null,
            'columns'           => null,
            'timestamp'         => true,
            'timestampFormat'   => 'Y-m-d H:i:s', // False would use Unixtime
        ];

        $vec_options = !empty($vec_options) ? ArrayHelper::merge($vec_default_options, $vec_options) : $vec_default_options;


        // Get the highest row and column numbers referenced in the worksheet
        $highest_row = $worksheet->getHighestRow();
        $offset_row = ($vec_options['rowOffset'] && $vec_options['rowOffset'] <= $highest_row) ? $vec_options['rowOffset'] : 0;
        $total_rows = ($vec_options['rows'] && ($offset_row + $vec_options['rows']) < $highest_row) ? $vec_options['rows'] : $highest_row - $offset_row;

        // Enhance performance for each getRow()
        $vec_options['columns'] = ($vec_options['columns']) ?: $this->alpha2num($worksheet->getHighestColumn());

        // Set row offset
        $this->$offset_row = $offset_row;

        // Fetch data from the sheet
        $vec_data = [];
        $vec_row = &$vec_data;
        for ( $i=1; $i <= $total_rows ; $i++ )
        {
            $row[] = $this->getRow($is_parse_string, $vec_options, $callback);
        }

        return $vec_data;
    }



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
