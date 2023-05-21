<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\auth\rbac;

use dezero\helpers\AuthHelper;
use Yii;

trait AuthTrait
{
    /*
    |--------------------------------------------------------------------------
    | ROLE methods
    |--------------------------------------------------------------------------
    */


    /**
     * Get all roles assigned to an user
     */
    public function getRoles()
    {
        return AuthHelper::getRolesByUser($this->getId());
    }


    /**
     * Check if an user is assigned to a role
     */
    public function hasRole(string $role_name) : bool
    {
        $role_item = AuthHelper::hasRole($role_name, $this->getId());

        return $role_item !== null;
    }


    /**
     * Assign a Role to an user
     */
    public function assignRole(string $role_name) : bool
    {
        // Role is already assigned to user
        if ( $this->hasRole($role_name) )
        {
            return true;
        }

        $role_item = AuthHelper::assignRole($role_name, $this->getId());

        return $role_item !== null;
    }


    /**
     * Revokes a role from an user
     */
    public function revokeRole(string $role_name) : bool
    {
        // Role is not assigned to user
        if ( ! $this->hasRole($role_name) )
        {
            return true;
        }

        return AuthHelper::revokeRole($role_name, $this->getId());
    }


    /**
     * Alias of revokeRole() method
     */
    public function removeRole(string $role_name) : bool
    {
        return $this->revokeRole($role_name);
    }


    /**
     * Check if current user belongs to Admin role
     */
    public function isAdmin()
    {
        return $this->hasRole('admin');
    }


    /*
    |--------------------------------------------------------------------------
    | PERMISSION methods
    |--------------------------------------------------------------------------
    */

    /**
     * Get all permissions assigned to an user
     */
    public function getPermissions()
    {
        return AuthHelper::getPermissionsByUser($this->getId());
    }


    /**
     * Check if an user is assigned to a permission
     */
    public function hasPermission(string $permission_name) : bool
    {
        $permission_item = AuthHelper::hasPermission($permission_name, $this->getId());

        return $permission_item !== null;
    }


    /**
     * Assign a Permission to an user
     */
    public function assignPermission(string $permission_name) : bool
    {
        // Permission is already assigned to user
        if ( $this->hasPermission($permission_name) )
        {
            return true;
        }

        $permission_item = AuthHelper::assignPermission($permission_name, $this->getId());

        return $permission_item !== null;
    }


    /**
     * Revokes a permission from an user
     */
    public function revokePermission(string $permission_name) : bool
    {
        // Permission is not assigned to user
        if ( ! $this->hasPermission($permission_name) )
        {
            return true;
        }

        return AuthHelper::revokePermission($permission_name, $this->getId());
    }


    /**
     * Alias of revokePermission() method
     */
    public function removePermission(string $permission_name) : bool
    {
        return $this->revokePermission($permission_name);
    }
}
