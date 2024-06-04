<?php
/**
 * I18nConfigurator class file
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
 * Base class to handle configuration options for I18N
 */
class I18nConfigurator extends Configurator implements ConfiguratorInterface
{
    /**
     * Load the configuration for a specific type
     */
    public function loadConfiguration() : array
    {
        $vec_config = Yii::$app->config->get('components/i18n');
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
        // Try with default configuration defined on "/app/config/i18n"
        $vec_config = Yii::$app->config->get('components/i18n');
        if ( $vec_config !== null )
        {
            return $vec_config;
        }

        return [
            // Source from i18n configuration is loaded: 'file' (default) or 'db'
            // 'source_type' => 'file',

            // Default language
            'default_language' => 'es-ES',

            // Extra supported languages
            'extra_languages' => [], // ['en-US','de'],    // <--- It defines a MULTI-LANGUAGE application
        ];
    }


    /**
     * Return default language
     */
    public function getDefaultLanguage() : string
    {
        return $this->get('default_language');
    }


    /**
     * Return extra languages
     */
    public function getExtraLanguages() : array
    {
        return $this->get('extra_languages');
    }
}
