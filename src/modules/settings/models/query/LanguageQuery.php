<?php
/**
 * LanguageQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\settings\models\query;

/**
 * ActiveQuery class for \dezero\modules\settings\models\Language.
 *
 * @see \dezero\modules\settings\models\Language
 */
class LanguageQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "language_id" attribute value
     */
    public function language_id(int $language_id) : self
    {
        return $this->andWhere(['language_id' => $language_id]);
    }

}
