<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */


namespace dezero\helpers;

use dezero\base\File;
use dezero\rest\Client;
use Dz;
use Yii;

/**
 * Helper class for HTTP requests
 */
class Http
{
    private static $vec_status_codes = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => 'Switch Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Payload Too Large',
        414 => 'URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        425 => 'Too Early',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        451 => 'Unavailable For Legal Reasons',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required'
    ];

    /**
     * Get all status codes
     */
    public static function getAllStatusCodes() : array
    {
        return self::$vec_status_codes;
    }


    /**
     * Get the message of a status code
     */
    public static function getStatusMessage(int $status_code, string $default_message = '') : string
    {
        return self::$vec_status_codes[$status_code] ?? $default_message;
    }


    /**
     * Download a file from a URL
     */
    public static function downloadFile(string $url, string $destination_path) : ?File
    {
        $client = Dz::makeObject(Client::class);
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->send();

        if ( ! $response->isOk )
        {
            return null;
        }

        // Save new downloaded file in the given destination path
        $new_download_file = File::load($destination_path);
        $new_download_file->write($response->content);

        return $new_download_file;

        /*
        // ALTERNATIVE VERSION WITH CURL -> DOES NOT WORK!!
        // @see https://www.yiiframework.com/extension/yiisoft/yii2-httpclient/doc/guide/2.0/en/basic-usage#downloading-files-using-curltransport
        $new_download_file = File::load($destination_path);
        $fh = fopen($new_download_file->realPath(), 'w');

        $client = Dz::makeObject(Client::class, [
            'transport' => 'yii\httpclient\CurlTransport'
        ]);
        $response = $client->createRequest()
            ->setMethod('GET')
            ->setUrl($url)
            ->setOutputFile($fh)
            ->send();
         */
    }
}
