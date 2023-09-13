<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\resources;

use dezero\rest\Resource;
use Yii;

class UsersResource extends Resource
{
    /**
     * Validate input parameters
     */
    public function validate() : bool
    {
        // Check auth validation from parent
        if ( ! parent::validate() )
        {
            return false;
        }

        return true;
    }


    /**
     * Run the resource
     */
    public function run()
    {
        // Prepare common REST API output
        $this->vec_response = [
            'status_code'   => 1,
            'errors'        => [],
            'total_results' => 0,
            'filters'       => new \stdClass,
            'results'       => [],
        ];
    }
}
