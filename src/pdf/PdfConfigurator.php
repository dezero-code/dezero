<?php
/**
 * PdfConfigurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\pdf;

use dezero\contracts\ConfiguratorInterface;
use dezero\base\Configurator;
use Yii;

/**
 * Base class to handle configuration options for the Wkhtmltopdf library
 */
class PdfConfigurator extends Configurator implements ConfiguratorInterface
{
    /**
     * Load the configuration for the Wkhtmltopdf library
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/pdf', $this->type);
        if ( $vec_config === null )
        {
            return [];
        }

        $this->vec_config = $vec_config;
        return $this->vec_config;
    }


        /**
     * Return the default configuration for the category type
     */
    public function defaultConfiguration() : array
    {
        // Try with default configuration defined on "/app/config/pdf"
        $vec_config = Yii::$app->config->get('components/pdf', 'default');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Binary path where "wkhtmltopdf" command is located
        	'binary'       => Yii::$app->params['wkhtmltopdf_binary_path'] ?? '/usr/local/bin/wkhtmltopdf',

            // Do not put an outline into the pdf. Make Chrome not complain
            'no-outline',

            // Temp directory
            'tmpDir'        => Yii::getAlias('@privateTmp'),

            // Margins
            'margin-top'    => 0,
            'margin-right'  => 0,
            'margin-bottom' => 0,
            'margin-left'   => 0,

            // Paper configuration
            'page-size'     => 'A4',
            'orientation'   => 'Portrait', // Landscape
            'dpi'           => '300',

            // Whether to ignore any errors if a PDF file was still created
            'ignoreWarnings' => true,

            // Language and encoding options
            'encoding' => 'UTF-8',
            'commandOptions' => [
                'useExec' => true,      // Can help if generation fails without a useful error message
                'procEnv' => [
                    // Check the output of 'locale -a' on your system to find supported languages
                    'LANG' => 'en_US.utf-8',
                ],
            ],

            // Disable the intelligent shrinking strategy used by WebKit that makes the pixel/dpi ratio none constant
            'disable-smart-shrinking',

            // Be less verbose, maintained for backwards compatibility; Same as using --log-level none
            'quiet'
        ];
    }


    /**
     * Return the binary path for the Wkhtmltopdf library
     */
    public function getBinaryPath() : string
    {
        return $this->get('binary');
    }


    /**
     * Return the page size
     */
    public function getPageSize() : string
    {
        return $this->get('page-size');
    }


    /**
     * Return the paper orientation
     */
    public function getOrientation() : string
    {
        return $this->get('orientation');
    }
}
