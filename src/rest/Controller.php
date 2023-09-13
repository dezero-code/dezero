<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\rest;
use dezero\web\Response;
use Dz;
use yii\filters\ContentNegotiator;
use yii\helpers\Json;
use Yii;

/**
 * Controller is the base class dor RESTful API controller classess
 */
class Controller extends \dezero\web\Controller
{
    /**
     * {@inheritdoc}
     */
    public $enableCsrfValidation = false;


    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'contentNegotiator' => [
                'class' => ContentNegotiator::className(),
                'formats' => [
                    'application/json' => Response::FORMAT_JSON,
                ],
            ],
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
                'cors' => [
                    'Origin' => ['*'],  // ['http://dezero.demo']
                    'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'DELETE'],
                    'Access-Control-Request-Headers' => ['*'],
                    'Access-Control-Allow-Credentials' => null,
                    'Access-Control-Max-Age' => 86400,
                    'Access-Control-Expose-Headers' => [],

                    // 'Access-Control-Allow-Methods' => ['GET', 'POST', 'PUT', 'DELETE'],
                    // 'Access-Control-Allow-Headers' => ['Origin', 'X-Requested-With', 'Content-Type', 'Accept', 'Authorization'],
                ],
            ],
            // 'verbFilter' => [
            //     'class' => VerbFilter::className(),
            //     'actions' => $this->verbs(),
            // ],
            // 'authenticator' => [
            //     'class' => CompositeAuth::className(),
            // ],
            // 'rateLimiter' => [
            //     'class' => RateLimiter::className(),
            // ],
        ];
    }


    /**
     * {@inhertidoc}
     */
    /*
    public function asJson($data)
    {
        return parent::asJson($data);
    }
    */


    /**
     * {@inheritdoc}
     */
    public function afterAction($action, $result)
    {
        $result = parent::afterAction($action, $result);

        if ( !empty($result) && is_array($result) )
        {
            return $this->asJson($result);
        }

        return $result;
    }
}
