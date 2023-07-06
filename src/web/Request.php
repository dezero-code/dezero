<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use Yii;


class Request extends \yii\web\Request
{
    /**
     * Remove one request parameter
     */
    public function removeBodyParam($param_name, $value_name = null) : void
    {
        $vec_values = $this->getBodyParams();
        if ( isset($vec_values[$param_name]) )
        {
            if ( $value_name !== null && isset($vec_values[$param_name][$value_name]) )
            {
                unset($vec_values[$param_name][$value_name]);
            }
            else
            {
                unset($vec_values[$param_name]);
            }

            $this->setBodyParams($vec_values);
        }
    }
}
