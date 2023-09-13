<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\StringHelper;
use Dz;
use Yii;


class Response extends \yii\web\Response
{
    /**
     * @inheritdoc \yii\web\Response::send()
     */
    public function send()
    {
        // Prepare response for API module
        if ( $this->isApiModule() )
        {
            // Force JSON
            $this->format = \yii\web\Response::FORMAT_JSON;
            $this->charset = 'UTF-8';

            // Errors from API module
            if ( $this->getIsError() || $this->data === null )
            {
                if ( $this->data === null )
                {
                    $this->setStatusCode(400);
                    $this->data['name'] = 'Empty response';
                }
                $error_message  = $this->data['name'] ?? '';
                $error_message .= isset($this->data['message']) ? ' - '. $this->data['message'] : '';

                $this->data = [
                    'status_code'   => $this->getStatusCode(),
                    'errors'        => [$error_message],
                    '_debug'        => $this->data
                ];
            }
        }

        parent::send();
    }


    /**
     * Check if current response is from API module
     */
    public function isApiModule()
    {
        $current_module = Dz::currentModule();
        return $current_module === 'api' || ( isset($this->data['module']) && $this->data['module'] === 'api' );

        // path_info = 'api/v1/...'
        // $path_info = Yii::$app->request->getPathInfo();
        // return preg_match("/^api\/v/", $path_info);
    }


    /*
     * @return bool whether this response indicates a client error
     */
    public function getIsError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 600;
    }


    /**
     * @inheritdoc \yii\web\Response::sendFile()
     * @param string $filePath
     * @param string|null $attachmentName
     * @param array $options
     * @return self self reference
     */
    public function sendFile($filePath, $attachmentName = null, $options = []) : self
    {
        $this->clearOutputBuffer();
        parent::sendFile($filePath, $attachmentName, $options);

        return $this;
    }

    /**
     * @inheritdoc \yii\web\Response::sendContentAsFile()
     * @param string $content
     * @param string $attachmentName
     * @param array $options
     * @return self self reference
     * @throws HttpException
     */
    public function sendContentAsFile($content, $attachmentName, $options = []) : self
    {
        $this->clearOutputBuffer();
        parent::sendContentAsFile($content, $attachmentName, $options);

        return $this;
    }


    /**
     * Clear the output buffer to prevent corrupt downloads.
     *
     * Need to check the OB status first, or else some PHP versions will throw an E_NOTICE
     * since we have a custom error handler (http://pear.php.net/bugs/bug.php?id=9670).
     *
     */
    private function clearOutputBuffer(): void
    {
        if (ob_get_length() !== false)
        {
            // If zlib.output_compression is enabled, then ob_clean() will corrupt the results of output buffering.
            // ob_end_clean is what we want.
            ob_end_clean();
        }
    }
}
