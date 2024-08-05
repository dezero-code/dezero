<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 *
 * @see https://github.com/mikehaertl/phpwkhtmltopdf
 * @see https://github.com/barryvdh/laravel-snappy/blob/master/src/PdfWrapper.php
 */

namespace dezero\pdf;

use dezero\base\File;
use dezero\contracts\ConfigInterface;
use dezero\pdf\PdfConfigurator;
use dezero\traits\ErrorTrait;
use Dz;
use mikehaertl\wkhtmlto\Pdf;
use Yii;
use yii\base\Exception;

/**
 * Class to build PDF files
 */
class PdfBuilder extends \yii\base\BaseObject implements ConfigInterface
{
    use ErrorTrait;


    /**
     * @var \dezero\pdf\PdfConfigurator
     */
    private $configurator;


    /**
     * @var Pdf
     */
    private $pdf;

    /**
     * @var string
     */
    private $destination_path;


    /**
     * @var string PDF type
     */
    protected $pdf_type;


	/**
	 * @var string HTML content to be added into to the PDF
	 */
	public $html;



    /**
     * Constructor
     */
    public function __construct(string $pdf_type = 'default')
    {
        $this->pdf = Dz::makeObject(Pdf::class);
        $this->pdf_type = $pdf_type;

        $this->init();
    }


    /**
     * Initializes the object
     */
    public function init() : void
    {
        // Custom destination path
        $this->destination_path = Yii::$app->params['pdf_destination_path'] ?? '@www/files/pdf';

        // Set global PDF options
        $this->pdf->setOptions($this->config->getFullConfig());
    }


    /**
     * Named constructor to create an empty new PDF object
     */
    public static function create(string $pdf_type = 'default') : self
    {
        return new static($pdf_type);
    }


    /*
    |--------------------------------------------------------------------------
    | CONFIGURATION BUILDER
    |--------------------------------------------------------------------------
    */

    /**
     * Return the Configurator class to manage configuration options
     */
    public function getConfig() : PdfConfigurator
    {
        if ( $this->configurator === null )
        {
            $this->configurator = Dz::makeObject(PdfConfigurator::class, [$this->pdf_type]);
        }

        return $this->configurator;
    }


    /**
     * Return the binary path for the Wkhtmltopdf library
     */
    public function getBinaryPath() : string
    {
        return $this->configurator ? $this->configurator->getBinaryPath() : Yii::$app->params['wkhtmltopdf_binary_path'] ?? '/usr/local/bin/wkhtmltopdf';
    }


    /*
    |--------------------------------------------------------------------------
    | OPTION METHODS
    |--------------------------------------------------------------------------
    */


    /**
     * Set options to PDF object
     */
    public function setOptions(array $vec_options) : Pdf
    {
        return $this->pdf->setOptions($vec_options);
    }


    /**
     * Set temporary path
     */
     public function setTemporaryPath(string $temp_path) : self
    {
         $this->setOptions(['tmpDir' => $temp_path]);

         return $this;
    }


    /**
     * Set destination path
     */
    public function setDestinationPath(string $path) : self
    {
        $this->destination_path = $path;

        return $this;
    }


    /**
     * Set the paper size (default A4)
     */
    public function setPaper(string $paper_size, ?string $orientation = null, ?int $dpi = null) : self
    {
        $vec_options = ['page-size' => $paper_size];

        if ( $orientation )
        {
            $vec_options['orientation'] = $orientation;
        }

        if ( $dpi )
        {
            $vec_options['dpi'] = $dpi;
        }

        $this->setOptions($vec_options);

        return $this;
    }


    /**
     * Set the orientation (default Portrait)
     */
    public function setOrientation($orientation)
    {
        $this->setOptions(['orientation' => $orientation]);

        return $this;
    }


    /**
     * Set HTML content to be saved or downloaded as PDF
     */
    public function setHtml(string $html) : self
    {
    	$this->html = $html;

        return $this;
    }


    /**
     * Render a view and set the HTML content
     */
    public function renderView(string $view, array $vec_params = []) : self
    {
        // Render the view
        $html = Yii::$app->view->render($view, $vec_params);

        $this->setHtml($html);

        return $this;
    }


    /**
     * Set the HTML content for the footer
     */
    public function setFooter(string $html) : self
    {
        $this->setOptions(['footer-html' => $html]);

        return $this;
    }


    /**
     * Set the HTML content for the footer from a file
     */
    public function setFooterPage(string $file_path) : self
    {
        $file = File::load($file_path);

        $this->setOptions(['footer-html' => $file->realPath()]);

        return $this;
    }


    /**
     * Generate the PDF and save it as file
     */
    public function saveTo(string $file_name, ?string $file_path = null, ?string $html = null) : ?File
    {
        // File path?
        if ( $file_path === null )
        {
            $file_path = Yii::getAlias($this->destination_path);
        }

        // Ensure directory exists & generate preset
        $file_directory = File::ensureDirectory($file_path);
        if ( ! $file_directory || ! $file_directory->exists() )
        {
            $this->addError("Directory '{$file_path}' does not exist");

            return null;
        }

        // Ensure filename has the ".pdf" extension
        $file_name = pathinfo($file_name, PATHINFO_EXTENSION) === 'pdf' ? $file_name : $file_name . '.pdf';

        // HTML content?
        if ( $html !== null )
        {
            $this->loadHtml($html);
        }

        $this->pdf->addPage($this->html);
        $pdf_file_path = $file_path . DIRECTORY_SEPARATOR . $file_name;
        $result = $this->pdf->saveAs($pdf_file_path);
        if ( $result  )
        {
            return File::load($pdf_file_path);
        }

        // Add error
        $this->addError($this->pdf->getError());

        return null;
    }


    /**
     * Download the generated PDF file
     */
    public function download(string $file_name = null, ?string $html = null) : void
    {
        // Save to the private TEMP directory
        $file_path = Yii::getAlias('@privateTmp');
        $pdf_file = $this->saveTo($file_name, $file_path, $html);

        if ( $pdf_file === null )
        {
            throw new Exception("PDF could not be generated");
        }

        // Finally, download the file (send to the browser)
        if ( $pdf_file->download() === null )
        {
            throw new Exception("PDF could not be downloaded");
        }

        Yii::$app->end();
    }


    /**
     * Display the generated PDF file
     */
    public function display(string $file_name = null, ?string $html = null) : void
    {
        // Save to the private TEMP directory
        $file_path = Yii::getAlias('@privateTmp');
        $pdf_file = $this->saveTo($file_name, $file_path, $html);

        if ( $pdf_file === null )
        {
            throw new Exception("PDF could not be generated");
        }

        // Finally, download the file (send to the browser)
        if ( $pdf_file->display() === null )
        {
            throw new Exception("PDF could not be downloaded");
        }

        Yii::$app->end();
    }
}
