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
use dezero\helpers\StringHelper;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Worksheet;
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
}
