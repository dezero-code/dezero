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
