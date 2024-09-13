<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\base\File;
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
     * @var bool|null whether the current user is a superadmin
     */
    private $is_superadmin = null;


    /**
     * @var string|array|null the URL for login when [[loginRequired()]] is called.
     */
    public $loginUrl = ['user/login'];


    /**
     * Alias of getIdentity() method
     */
    public function getModel() : ?IdentityInterface
    {
        return $this->getIdentity();
    }


    /**
     * Check if current user belongs to Admin role and has been marked as SUPERADMIN
     */
    public function isSuperadmin() : bool
    {
        // If already checked, return the value
        if ( $this->is_superadmin !== null )
        {
            return $this->is_superadmin;
        }

        // Check if the user is a superadmin
        $user_model = $this->getModel();
        $this->is_superadmin = $user_model ? $user_model->isSuperadmin() : false;

        return $this->is_superadmin;
    }


    /**
     * Returns the language of the user
     */
    public function getUserLanguage() : string
    {
        $default_language = getenv('SITE_LANGUAGE');

        // Return the default language for guests
        if ( $this->isGuest )
        {
            return $default_language;
        }

        $user_model = $this->getModel();
        if ( $user_model === null )
        {
            return $default_language;
        }

        return $user_model->language_id;
    }


    /**
     * Redirects the user browser away from a guest page.
     */
    public function guestRequired(?array $default_url = null) : Response
    {
        if ( !$this->checkRedirectAcceptable() )
        {
            throw new ForbiddenHttpException(Yii::t('backend', 'Not registered user is allowed'));
        }

        return Yii::$app->getResponse()->redirect($this->getReturnUrl($default_url));
    }


    /**
     * Return TEMP directory for this user
     */
    public function getTempDirectory() : File
    {
        return File::ensureDirectory('@tmp'. DIRECTORY_SEPARATOR . Yii::$app->session->id);
    }


    /**
     * {@inheritdoc}
     */
    public function can($permissionName, $params = [], $allowCaching = true)
    {
        // Superadmin can do everything
        if ( $this->isSuperadmin() )
        {
            return true;
        }

        return parent::can($permissionName, $params, $allowCaching);
    }
}
