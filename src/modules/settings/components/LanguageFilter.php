<?php
/**
 * LanguageFilter class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\settings\components;

use Yii;
use yii\base\ActionFilter;


/**
 * LanguageFilter class
 */
class LanguageFilter extends ActionFilter
{
    /**
     * Set the language for the current user
     */
    public function beforeAction($action)
    {
        Yii::$app->language = Yii::$app->user->getUserLanguage();

        return parent::beforeAction($action);
    }
}
