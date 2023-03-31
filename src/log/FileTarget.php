<?php
/**
 * FileTarget class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\log;

use Yii;

/**
 * FileTarget records log messages in a file.
 */
class FileTarget extends \yii\log\FileTarget
{
    /**
     * Initializes the route.
     * This method is invoked after the route is created by the route manager.
     */
    public function init()
    {
        // App.log by default
        if ( $this->logFile === null )
        {
            $this->logFile = 'app.log';
        }

        // Set full log path, if it isn't added
        if ( ! preg_match("/\@/", $this->logFile) )
        {
            $log_path = isset(Yii::$app->params['logPath']) ? Yii::$app->params['logPath'] : '@storage/logs';
            $this->logFile = $log_path . '/' . $this->logFile;
        }

        // Remove LogVars by default
        $this->logVars = [];

        // To make each log message appear immediately in the log targets
        $this->exportInterval = 1;

        parent::init();
    }
}
