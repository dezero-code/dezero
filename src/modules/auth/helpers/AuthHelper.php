<?php
/**
 * Auth class helper
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\helpers;

use dezero\modules\auth\rbac\Assignment;
use dezero\modules\auth\rbac\Permission;
use dezero\modules\auth\rbac\Role;
use Dz;
use user\models\User;
use Yii;

/**
 * Helper class for working with RBAC items
 */
class AuthHelper
{
   /*
    |--------------------------------------------------------------------------
    | ROLE methods
    |--------------------------------------------------------------------------
    */


    /**
     * Creates a new role into the RBAC system
     */
    public static function createRole(string $name, ?string $description = null) : bool
    {
        $role_item = Dz::makeObject(Role::class);
        $role_item->name = $name;
        $role_item->description = $description;

        return Yii::$app->authManager->add($role_item);
    }


    /**
     * Return a role by name
     */
    public static function getRole(string $name)
    {
        return Yii::$app->authManager->getRole($name);
    }


    /**
     * Return all the roles items
     */
    public static function getRoles() : array
    {
        return Yii::$app->authManager->getRoles();
    }


    /**
     * Return all the roles as an array list
     */
    public static function getRolesList() : array
    {
        $vec_role_items = self::getRoles();

        $vec_roles = [];
        if ( !empty($vec_role_items) )
        {
            foreach ( $vec_role_items as $role_item )
            {
                $vec_roles[$role_item->name] = $role_item->description;
            }
        }

        return $vec_roles;
    }


    /**
     * Removes a role from the RBAC system
     */
    public static function removeRole(string $name) : bool
    {
        $role_item = self::getRole($name);
        if ( $role_item === null )
        {
            return false;
        }

        return Yii::$app->authManager->remove($role_item);
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSION methods
    |--------------------------------------------------------------------------
    */


    /**
     * Creates a new permission into the RBAC system
     */
    public static function createPermission(string $name, ?string $description = null) : bool
    {
        $permission_item = Dz::makeObject(Permission::class);
        $permission_item->name = $name;
        $permission_item->description = $description;

        return Yii::$app->authManager->add($permission_item);
    }


    /**
     * Return a permission by name
     */
    public static function getPermission(string $name)
    {
        return Yii::$app->authManager->getPermission($name);
    }


    /**
     * Return all the permissions
     */
    public static function getPermissions()
    {
        return Yii::$app->authManager->getPermissions();
    }


    /**
     * Removes a permission from the RBAC system
     */
    public static function removePermission(string $name) : bool
    {
        $permission_item = self::getPermission($name);
        if ( $permission_item === null )
        {
            return false;
        }

        return Yii::$app->authManager->remove($permission_item);
    }


    /*
    |--------------------------------------------------------------------------
    | ROLES & PERMISSIONS methods
    |--------------------------------------------------------------------------
    */


    /**
     * Returns all permissions that the specified role represents
     */
    public static function getPermissionsByRole(string $role_name) : array
    {
        return Yii::$app->authManager->getPermissionsByRole($role_name);
    }


    /**
     * Assign a permission to a role
     */
    public static function assignPermissionToRole(string $role_name, string $permission_name) : bool
    {
        $role_item = self::getRole($role_name);
        $permission_item = self::getPermission($permission_name);

        if ( $role_item === null || $permission_item === null )
        {
            return false;
        }

        return Yii::$app->authManager->addChild($role_item, $permission_item);
    }


    /**
     * Revoke permission to a role
     */
    public static function revokePermissionToRole(string $role_name, string $permission_name) : bool
    {
        $role_item = self::getRole($role_name);
        $permission_item = self::getPermission($permission_name);

        if ( $role_item === null || $permission_item === null )
        {
            return false;
        }

        return Yii::$app->authManager->removeChild($role_item, $permission_item);
    }


    /*
    |--------------------------------------------------------------------------
    | ROLES & USERS methods
    |--------------------------------------------------------------------------
    */


    /**
     * Returns the roles that are assigned to the user
     */
    public static function getRolesByUser(int $user_id) : array
    {
        return Yii::$app->authManager->getRolesByUser($user_id);
    }


    /**
     * Check if an user is assigned to a role
     */
    public static function hasRole(string $role_name, int $user_id) : ?Assignment
    {
        return Yii::$app->authManager->getAssignment($role_name, $user_id);
    }


    /**
     * Assign a Role to an user
     */
    public static function assignRole(string $role_name, int $user_id) : ?Assignment
    {
        $role_item = self::getRole($role_name);
        $user_model = User::findOne($user_id);

        if ( $role_item === null || $user_model === null )
        {
            return null;
        }

        return Yii::$app->authManager->assign($role_item, $user_model->user_id);
    }


    /**
     * Revokes a role from an user
     */
    public static function revokeRole(string $role_name, int $user_id) : bool
    {
        $role_item = self::getRole($role_name);
        $user_model = User::findOne($user_id);

        if ( $role_item === null || $user_model === null )
        {
            return false;
        }

        return Yii::$app->authManager->revoke($role_item, $user_model->user_id);
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSIONS & USERS methods
    |--------------------------------------------------------------------------
    */


    /**
     * Returns all permissions that the user has
     */
    public static function getPermissionsByUser(int $user_id) : array
    {
        return Yii::$app->authManager->getPermissionsByUser($user_id);
    }


    /**
     * Check if an user is assigned to a permission
     */
    public static function hasPermission(string $permission_name, int $user_id) : ?Assignment
    {
        return Yii::$app->authManager->getAssignment($permission_name, $user_id);
    }


    /**
     * Assign a Permission to an user
     */
    public static function assignPermission(string $permission_name, int $user_id) : ?Assignment
    {
        $permission_item = self::getPermission($permission_name);
        $user_model = User::findOne($user_id);

        if ( $permission_item === null || $user_model === null )
        {
            return null;
        }

        return Yii::$app->authManager->assign($permission_item, $user_model->user_id);
    }


    /**
     * Revokes a permission from an user
     */
    public static function revokePermission(string $permission_name, int $user_id) : bool
    {
        $permission_item = self::getPermission($permission_name);
        $user_model = User::findOne($user_id);

        if ( $permission_item === null || $user_model === null )
        {
            return false;
        }

        return Yii::$app->authManager->revoke($permission_item, $user_model->user_id);
    }
}
