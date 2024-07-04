<?php
/**
 * Auth checker class
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2024 Fabián Ruiz
 */

namespace dezero\modules\auth\helpers;

use yii\web\ForbiddenHttpException;
use Yii;
use yii\web\Response;

/**
 * Helper class for authorization checkings
 */
class AuthChecker
{
    /**
     * Redirects the user to the login page if is not logged in
     */
    public static function requireLogin() : void
    {
        // User is already logged in
        if ( ! Yii::$app->user->isGuest )
        {
            return;
        }

        // AJAX request, send a JSON response
        if ( Yii::$app->request->isAjax )
        {
            Yii::$app->response->format = Response::FORMAT_JSON;
            Yii::$app->response->statusCode = 200;
            Yii::$app->response->data = [
                'status'    => 'session_expired',
                'message'   => Yii::t('backend', 'Your session has expired. Please log in again.')
            ];
            Yii::$app->response->send();
            Yii::$app->end();
        }

        // Redirect to login page
        Yii::$app->user->loginRequired();
        Yii::$app->end();
    }


    /**
     * Redirects the user to the account template if they are logged in
     */
    public static function requireGuest() : void
    {
        if ( Yii::$app->user->isGuest )
        {
            return;
        }

        Yii::$app->user->guestRequired();
        Yii::$app->end();
    }


    /**
     * Checks if the current user is an admin. If not, throws a 403 error
     */
    public static function requireAdmin() : void
    {
        // First of all, ensure user is logged in
        self::requireLogin();

        // Check if is admin
        if ( ! Yii::$app->user->isAdmin() )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Checks if the current user is an superadmin. If not, throws a 403 error
     */
    public static function requireSuperadmin() : void
    {
        // First of all, ensure user is logged in
        self::requireLogin();

        // Check if is superadmin
        if ( ! Yii::$app->user->isSuperadmin() )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Checks if the current user belongs to a specific role. If not, throws a 403 error
     */
    public static function requireRole(string $role_name, bool $is_skip_superadmin = true) : void
    {
        if ( ( ! $is_skip_superadmin || ! Yii::$app->user->isSuperadmin() ) && ! Yii::$app->user->hasRole($role_name) )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Checks if the current user has permission to perform a given action. If not, throws a 403 error
     */
    public static function requirePermission(string $permission_name, bool $is_skip_superadmin = true) : void
    {
        if ( ( ! $is_skip_superadmin || ! Yii::$app->user->isSuperadmin() ) && ! Yii::$app->user->can($permission_name) )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }


    /**
     * Check if the user has at least one of the specified permissions to perform an action. If not, throws a 403 error
     */
    public static function requirePermissions(array $vec_permission_names, bool $is_skip_superadmin = true) : void
    {
        $is_allowed = false;
        foreach ( $vec_permission_names as $permission_name )
        {
            if ( Yii::$app->user->can($permission_name) )
            {
                $is_allowed = true;
                break;
            }
        }

        if ( ( ! $is_skip_superadmin || ! Yii::$app->user->isSuperadmin() ) && ! $is_allowed )
        {
            throw new ForbiddenHttpException('You are not allowed to access this page.');
        }
    }
}
