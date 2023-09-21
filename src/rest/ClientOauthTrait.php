<?php
/**
 * Trait with Oauth methods
 *
 * @see dezero\traits\FlashMessageTrait
 */

namespace dezero\rest;

use dezero\base\File;
use dezero\helpers\ArrayHelper;
use yii\helpers\Json;
use Yii;

trait ClientOauthTrait
{
    /**
     * @var \dezero\base\File
     */
    public $token_file;


    /**
     * Return access token (OAuth)
     */
    public function getAccessToken(?string $temp_file_name = null, int $expired_time = 86400) : string
    {
        // Get token information saved on /storage/tmp/<file>.json file
        if ( ! $this->loadTokenFile($temp_file_name) )
        {
            return '';
        }

        $now = time();
        $data_json = $this->token_file->read();

        $is_request_token = true;
        if ( !empty($data_json) )
        {
            $vec_data = Json::decode($data_json);
            if ( !empty($vec_data) && isset($vec_data['access_token']) && is_array($vec_data) && isset($vec_data['expiration_date']) && $vec_data['expiration_date'] > $now )
            {
                $is_request_token = false;
            }
        }

        // Empty file or expired token ---> Request new token and save on the file
        if ( $is_request_token )
        {
            $vec_data = $this->requestNewToken();
            $vec_data['expiration_date'] = $now + $expired_time;

            $this->token_file->write(Json::encode($vec_data));
        }

        // Return access token
        if ( isset($vec_data['access_token']) )
        {
            return 'Bearer '. $vec_data['access_token'];
        }

        return '';
    }


    /**
     * Request new access token via API
     */
    public function requestNewToken() : array
    {
        return [];
    }


    /**
     * Load the file "/storage/tmp/bc.json"
     */
    private function loadTokenFile(?string $temp_file_name = null) : bool
    {
        if ( $temp_file_name === null )
        {
            $temp_file_name = 'client.json';
        }
        $token_file_path = Yii::getAlias('@privateTmp') . DIRECTORY_SEPARATOR . $temp_file_name;

        if ( empty($this->token_file) )
        {
            $this->token_file = File::load($token_file_path);
        }

        if ( $this->token_file && ( $this->token_file->exists() || $this->token_file->createEmptyFile() ) )
        {
            return true;
        }

        return false;
    }
}
