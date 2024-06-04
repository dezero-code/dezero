<?php
/**
 * LocaleConfigurator class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\i18n;

use dezero\base\Configurator;
use dezero\contracts\ConfiguratorInterface;
use Yii;

/**
 * Base class to handle configuration options for L10N
 */
class LocaleConfigurator extends Configurator implements ConfiguratorInterface
{
    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/locale');
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
        // Try with default configuration defined on "/app/config/locale"
        $vec_config = Yii::$app->config->get('components/locale');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            'default_currency' => 'EUR',
        ];
    }


    /**
     * Return default currency
     */
    public function getDefaultCurrency() : string
    {
        return $this->get('default_currency');
    }
}
