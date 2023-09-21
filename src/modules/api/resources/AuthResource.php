<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\api\resources;

use dezero\rest\Resource;
use Yii;

class AuthResource extends Resource
{
    /**
     * Constructor
     */
    public function __construct(string $api_name = 'default', array $vec_config = [])
    {
        parent::__construct($api_name, $vec_config);
    }


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

        // Require params
        $vec_required_params = ['client_id', 'client_secret'];
        if ( ! $this->validateRequired($vec_required_params) )
        {
            return false;
        }

        // Check credentials
        if ( ! $this->checkCredentials() )
        {
            $this->addError('Unauthorized', 401);
            return false;
        }

        return true;
    }


    /**
     * Check credentials
     */
    private function checkCredentials() : bool
    {
        return $this->getInput('client_id') === 'test' && $this->getInput('client_secret') === 'test00';
    }


    /**
     * Run the resource
     */
    public function run() : void
    {
        // Dummy token
        $this->vec_response['status_code'] = 1;
        $this->vec_response['errors'] = [];
        $this->vec_response['access_token'] = 'h7P7lwXGun1rY9jtewsRTIrln3CeHRw?MJSig?eTXqYjk/hkNR2ao06w7XNI1=VV6F/w-PTH12n77INs-aug!6n2Z4!a2KZWmv0HqMS6pvtw3HSnknBpi8-LQz5Zvv?lKNkQ=9hV5Wk!khbF9t=T8Yhkaz-G8l4uLOUt9ZAJB0Pl7kaSODjH562Ainj?BpKp2fWjQQKLWC4!bfeyTtlSYustSARh-0G4fXh!agKcO?gvz3o?XG4SqBlUy4oB9Ndy';
        $this->vec_response['token_type'] = 'Bearer';
        $this->vec_response['expires_in'] = 86400;
    }
}
