<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\StringHelper;
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


    /**
     * Return a specific POST parameter with a given model and attribute
     */
    public function postAttribute($model, $attribute)
    {
        $vec_post_data = $this->post(StringHelper::basename($model::className()));
        if ( !empty($vec_post_data) && isset($vec_post_data[$attribute]) )
        {
            return $vec_post_data[$attribute];
        }

        return null;
    }


    /**
     * Get URL segments as an array
     */
    public function getSegments() : array
    {
        $pathInfo = $this->getPathInfo();

        return $pathInfo ? explode('/', trim($pathInfo, '/')) : [];
    }


    /**
     * Get a specific segment by its index
     *
     * @param int $index The index of the segment to retrieve (0-based)
     * @param mixed $default The default value to return if the segment doesn't exist
     * @return mixed The segment value or the default value if not found
     */
    public function getSegment(int $index, $default = null)
    {
        $segments = $this->getSegments();

        return $segments[$index] ?? $default;
    }
}
