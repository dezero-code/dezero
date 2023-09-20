<?php
/**
 * FileTarget class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2022 Fabián Ruiz
 */

namespace dezero\log;

use yii\helpers\FileHelper;
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


    /**
     * Writes log messages to a file.
     * Starting from version 2.0.14, this method throws LogRuntimeException in case the log can not be exported.
     * @throws InvalidConfigException if unable to open the log file for writing
     * @throws LogRuntimeException if unable to write complete log to file
     */
    public function export()
    {
        $text = implode("\n", array_map([$this, 'formatMessage'], $this->messages)) . "\n";
        $trimmedText = trim($text);

        if (empty($trimmedText)) {
            return; // No messages to export, so we exit the function early
        }

        // DEZERO - Add a break line at the beginning and at the end
        $trimmedText = "\n". $trimmedText ."\n\n---\n";

        if (strpos($this->logFile, '://') === false || strncmp($this->logFile, 'file://', 7) === 0) {
            $logPath = dirname($this->logFile);
            FileHelper::createDirectory($logPath, $this->dirMode, true);
        }

        if (($fp = @fopen($this->logFile, 'a')) === false) {
            throw new InvalidConfigException("Unable to append to log file: {$this->logFile}");
        }
        @flock($fp, LOCK_EX);
        if ($this->enableRotation) {
            // clear stat cache to ensure getting the real current file size and not a cached one
            // this may result in rotating twice when cached file size is used on subsequent calls
            clearstatcache();
        }
        if ($this->enableRotation && @filesize($this->logFile) > $this->maxFileSize * 1024) {
            $this->rotateFiles();
        }
        $writeResult = @fwrite($fp, $trimmedText);
        if ($writeResult === false) {
            $error = error_get_last();
            throw new LogRuntimeException("Unable to export log through file ({$this->logFile})!: {$error['message']}");
        }
        $textSize = strlen($trimmedText);
        if ($writeResult < $textSize) {
            throw new LogRuntimeException("Unable to export whole log through file ({$this->logFile})! Wrote $writeResult out of $textSize bytes.");
        }
        @fflush($fp);
        @flock($fp, LOCK_UN);
        @fclose($fp);

        if ($this->fileMode !== null) {
            @chmod($this->logFile, $this->fileMode);
        }
    }
}
