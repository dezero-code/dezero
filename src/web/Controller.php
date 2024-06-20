<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;
use dezero\helpers\Json;
use dezero\modules\auth\helpers\AuthChecker;
use dezero\validators\AjaxRequestValidator;
use Dz;
use yii\web\BadRequestHttpException;
use yii\web\Response;
use Yii;

/**
 * Controller is the base class of web controllers.
 */
class Controller extends \yii\web\Controller
{
    public function beforeAction($action)
    {
        return parent::beforeAction($action);
    }


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
        AuthChecker::requireLogin();
    }


    /**
     * Redirects the user to the account template if they are logged in
     */
    public function requireGuest() : void
    {
        AuthChecker::requireGuest();
    }


    /**
     * Checks if the current user is an admin. If not, throws a 403 error
     */
    public function requireAdmin() : void
    {
        AuthChecker::requireAdmin();
    }


    /**
     * Checks if the current user is an superadmin. If not, throws a 403 error
     */
    public function requireSuperadmin() : void
    {
        AuthChecker::requireSuperadmin();
    }


    /**
     * Checks if the current user belongs to a specific role. If not, throws a 403 error
     */
    public function requireRole(string $role_name, bool $is_skip_superadmin = true) : void
    {
        AuthChecker::requireRole($role_name, $is_skip_superadmin);
    }


    /**
     * Checks if the current user has permission to perform a given action. If not, throws a 403 error
     */
    public function requirePermission(string $permission_name, bool $is_skip_superadmin = true) : void
    {
        AuthChecker::requirePermission($permission_name, $is_skip_superadmin);
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
        if ( ! $this->request->getAcceptsJson() && ! $this->request->getIsOptions() )
        {
            throw new BadRequestHttpException('Request must accept JSON in response');
        }
    }


    /**
     * Throws a 400 error if this isn’t an AJAX request
     */
    public function requireAjaxRequest() : void
    {
        if ( ! $this->request->getIsAjax() )
        {
            throw new BadRequestHttpException('Ajax request required');
        }
    }


    /**
     * Validate model via AJAX
     */
    public function validateAjaxRequest($model) : void
    {
        $is_valid  = Dz::makeObject(AjaxRequestValidator::class, [$model])->validate();
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
