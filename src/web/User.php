<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\modules\auth\rbac\AuthTrait;
use yii\web\IdentityInterface;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;

/**
 * The User component provides APIs for managing the user authentication status.
 */
class User extends \yii\web\User
{
    use AuthTrait;

    /**
     * @var string|array|null the URL for login when [[loginRequired()]] is called.
     */
    public $loginUrl = ['user/login'];


    /**
     * Alias of getIdentity() method
     */
    public function getModel() : IdentityInterface
    {
        return $this->getIdentity();
    }


    /**
     * Redirects the user browser away from a guest page.
     */
    public function guestRequired(?array $default_url = null) : Response
    {
        if ( !$this->checkRedirectAcceptable() )
        {
            throw new ForbiddenHttpException(Yii::t('app', 'Not registered user is allowed'));
        }

        return Yii::$app->getResponse()->redirect($this->getReturnUrl($default_url));
    }
}
