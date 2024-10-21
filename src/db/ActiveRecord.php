<?php
/**
 * ActiveRecord class file (extends from Model)
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\db;

use Yii;

/**
 * ActiveRecord is the base class for classes representing relational data in terms of objects.
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * @var bool
     */
    private $isClearAttributes = false;

    /**
     * Constructor
     */
    public function __construct(array $vec_config = [])
    {
        /**
         * Clear attributes? If yes, DO NOT execute "loadDefaultValues()" method
         *
         * @see Dz::makeCleanObject()
         */
        if ( isset($vec_config['clearAttributes']) )
        {
            if ( $vec_config['clearAttributes'] === true )
            {
                $this->isClearAttributes = true;
            }

            unset($vec_config['clearAttributes']);
        }

        parent::__construct($vec_config);
    }


    /**
     * {@inheritdoc}
     */
    public function init() : void
    {
        parent::init();

        // Load default values by default
        if ( $this->getIsNewRecord() && $this->isClearAttributes === false )
        {
            // 12/02/2024 - Fix an error generating active records templates from Gii
            $table_name = static::tableName();
            if ( $table_name !== '{{%active_record}}' )
            {
                $this->loadDefaultValues();
            }
        }
    }


    /**
     * Saves a selected list of attributes without validation
     *
     * Alias from BaseActiveRecord::updateAttributes()
     */
    public function saveAttributes(array $vec_attributes)
    {
        return $this->updateAttributes($vec_attributes);
    }


    /**
     * {@inheritdoc}
     * @return static|null ActiveRecord instance matching the condition, or `null` if nothing matches.
     */
    public static function findOneNoCache($condition)
    {
        return parent::findByCondition($condition)->cache(false)->one();
    }
}
