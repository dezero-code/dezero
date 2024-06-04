<?php
/**
 * I18N provides features related with internationalization (I18N).
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\i18n;

use dezero\i18n\I18nConfigurator;
use dezero\modules\settings\models\Language;
use Yii;

class I18n extends \yii\i18n\I18N
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Return an array with all the languages available in the platform.
     */
    public function getLanguageList() : array
    {
        $vec_language_models = Language::find()
            ->enabled()
            ->orderBy([
                'weight'    => SORT_ASC
            ])
            ->all();

        $vec_output = [];
        foreach ( $vec_language_models as $language_model )
        {
            $vec_output[$language_model->language_id] = $language_model->title();
        }

        return $vec_output;
    }


    /**
     * Return the default language for the platform.
     */
    public function getDefaultLanguage() : string
    {
        return Yii::$app->config->get('components/i18n', 'default_language', 'en-US');
    }
}
