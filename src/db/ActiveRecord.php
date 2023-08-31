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
     * {@inheritdoc}
     */
    public function init(): void
    {
        parent::init();

        // Load default values by default
        if ( $this->isNewRecord )
        {
            $this->loadDefaultValues();
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
}
