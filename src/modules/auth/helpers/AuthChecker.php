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
        if ( Yii::$app->user->getIsGuest() )
        {
            Yii::$app->user->loginRequired();
            Yii::$app->end();
        }
    }


    /**
     * Redirects the user to the account template if they are logged in
     */
    public static function requireGuest() : void
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
}
