<?php
/**
 * L10n provides features related with localization (L10N).
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\i18n;

use dezero\l10n\L10nConfigurator;
use dezero\modules\settings\models\Currency;
use Yii;

class Locale extends \yii\i18n\Locale
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();
    }


    /**
     * Return an array with all the currencies available in the platform.
     */
    public function getCurrencyList() : array
    {
        $vec_currency_models = Currency::find()
            ->enabled()
            ->orderBy([
                'weight'    => SORT_ASC
            ])
            ->all();

        $vec_output = [];
        foreach ( $vec_currency_models as $currency_model )
        {
            $vec_output[$currency_model->currency_code] = $currency_model->fullTitle();
        }

        return $vec_output;
    }


    /**
     * Return the default currency for the platform.
     */
    public function getDefaultCurrency() : string
    {
        return Yii::$app->config->get('components/l10n', 'default_currency', 'EUR');
    }
}
