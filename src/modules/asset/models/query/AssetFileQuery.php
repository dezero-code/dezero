<?php
/**
 * AssetFileQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\asset\models\query;

/**
 * ActiveQuery class for \dezero\modules\asset\models\AssetFile.
 *
 * @see \dezero\modules\asset\models\AssetFile
 */
class AssetFileQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "file_id" attribute value
     */
    public function file_id(int $file_id) : self
    {
        return $this->andWhere(['file_id' => $file_id]);
    }


    /**
     * Filter the query by "file_name" attribute value
     */
    public function file_name(string $file_name) : self
    {
        return $this->andWhere(['file_name' => $file_name]);
    }
}
