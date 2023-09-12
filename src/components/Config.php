<?php
/**
 * Config component class
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\components;

use yii\base\Component;
use Yii;

/**
 * Component class for working with several config files
 */
class Config extends Component
{
    /**
     * Find a config file inside @app.config alias path and return the content
     * 
     * @return mixed
     */
    public function get(string $config_path, ?string $config_key = null)
    {
        if ( preg_match("/^\@/", $config_path) )
        {
            $config_file_path = Yii::getAlias($config_path) .'.php';
        }
        else
        {
            // Given a simple file like "routes"
            if ( strpos($config_path, DIRECTORY_SEPARATOR) === false)
            {
                $config_file_path = Yii::getAlias('@config/components/'. $config_path) .'.php';
            }

            // Given a path like "common/aliases"
            else
            {
                $config_file_path = Yii::getAlias('@config/'. $config_path) .'.php';
            }
        }

        if ( is_file($config_file_path) )
        {
            // Get configuration file content
            $vec_config = require($config_file_path);
            
            // Return just the content from a configuration key
            if ( !empty($vec_config) && is_array($vec_config) && $config_key !== null )
            {
                if ( ! array_key_exists($config_key, $vec_config) )
                {
                    return null;
                }

                return $vec_config[$config_key];
            }

            return $vec_config;
        }

        return null;
    }


    /**
     * Get MySQL database configuration
     */
    public function getDb()
    {
        $vec_config = [];

        // If the DSN is already set, parse it
        if ( Yii::$app->db->dsn && ($pos = strpos(Yii::$app->db->dsn, ':')) !== false )
        {
            $vec_config['driver'] = substr(Yii::$app->db->dsn, 0, $pos);
            $params = substr(Yii::$app->db->dsn, $pos + 1);
            foreach ( explode(';', $params) as $param )
            {
                if ( ($pos = strpos($param, '=')) !== false )
                {
                    $param_name = substr($param, 0, $pos);
                    $param_value = substr($param, $pos + 1);
                    switch ($param_name)
                    {
                        case 'host':
                            $vec_config['server'] = $param_value;
                            break;
                        case 'port':
                            $vec_config['port'] = $param_value;
                            break;
                        case 'dbname':
                            $vec_config['database'] = $param_value;
                            break;
                        case 'unix_socket':
                            $vec_config['unixSocket'] = $param_value;
                            break;
                        case 'charset':
                            $vec_config['charset'] = $param_value;
                            break;
                        case 'user': // PG only
                            $vec_config['user'] = $param_value;
                            break;
                        case 'password': // PG only
                            $vec_config['password'] = $param_value;
                            break;
                    }
                }
            }

            // Set the port
            if ( !isset($vec_config['port']) && isset($vec_config['driver']) && $vec_config['driver'] == 'mysql' )
            {
                $vec_config['port'] = 3306;
            }
            else if ( isset($vec_config['port']) )
            {
                $vec_config['port'] = (int)$vec_config['port'];
            }

            // Username, password and charset
            $vec_config['username'] = $vec_config['username'] ?? Yii::$app->db->username;
            $vec_config['password'] = $vec_config['password'] ?? Yii::$app->db->password;
            $vec_config['charset'] = $vec_config['charset'] ?? Yii::$app->db->charset;
        }

        return $vec_config;
    }
}
