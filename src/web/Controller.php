<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use dezero\helpers\Json;
use dezero\validators\AjaxRequestValidator;
use Dz;
use yii\web\BadRequestHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\Response;
use Yii;

/**
 * Controller is the base class of web controllers.
 */
class Controller extends \yii\web\Controller
{
    /**
     * Return an array decoded from a JSON input data
     */
    public function jsonInput()
    {
        // return Json::decode(file_get_contents('php://input'));
        return Json::decode($this->request->getRawBody());
    }


    /**
     * {@inheritdoc}
     */
    public function redirect($url, $statusCode = 302) : Response
    {
        // If not URL is present redirect to the browser home page
        if ( $url === null )
        {
            return $this->goHome();
        }

        return $this->response->redirect($url, $statusCode);
    }


    /**
     * Redirects the user to the login page if is not logged in
     */
    public function requireLogin() : void
    {
        if ( Yii::$app->user->getIsGuest() )
        {
            Yii::$app->user->loginRequired();
            Yii::$app->end();
        }
    }


    /**
     * Redirects the user to the account template if they are logged in
     */
    public function requireGuest() : void
    {
        if ( ! Yii::$app->user->getIsGuest() )
        {
            Yii::$app->user->guestRequired();
            Yii::$app->end();
        }
    }


    /**
     * Checks if the current user is an admin. If not, throws a 403 error
     */
    public function requireAdmin() : void
    {
        // First of all, ensure user is logged in
        $this->requireLogin();

        // Check if is admin
        if ( ! Yii::$app->user->isAdmin() )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Checks if the current user belongs to a specific role. If not, throws a 403 error
     */
    public function requireRole(string $role_name, bool $is_skip_superadmin = true) : void
    {
        if ( ( ! $is_skip_superadmin || ! Yii::$app->user->isSuperadmin() ) && ! Yii::$app->user->hasRole($role_name) )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Checks if the current user has permission to perform a given action. If not, throws a 403 error
     */
    public function requirePermission(string $permission_name, bool $is_skip_superadmin = true) : void
    {
        if ( ( ! $is_skip_superadmin || ! Yii::$app->user->isSuperadmin() ) && ! Yii::$app->user->can($permission_name) )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Throws a 400 error if this isn’t a POST request
     */
    public function requirePostRequest() : void
    {
        if ( ! $this->request->getIsPost() )
        {
            throw new BadRequestHttpException('Post request required');
        }
    }


    /**
     * Throws a 400 error if the request doesn't accept JSON.
     */
    public function requireAcceptsJson() : void
    {
        if ( !$this->request->getAcceptsJson() && !$this->request->getIsOptions() )
        {
            throw new BadRequestHttpException('Request must accept JSON in response');
        }
    }


    /**
     * Validate model via AJAX
     */
    public function validateAjaxRequest($model) : void
    {
        Dz::makeObject(AjaxRequestValidator::class, [$model])->validate();
    }


    /**
     * Send data as RAW (not formatted)
     */
    public function asRaw($data): Response
    {
        $this->response->data = $data;
        $this->response->format = Response::FORMAT_RAW;
        return $this->response;
    }
}
