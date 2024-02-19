<?php
/*
|-----------------------------------------------------------------
| Controller class for testing AuthHelper, roles & permissions
|-----------------------------------------------------------------
*/

namespace dezero\modules\test\controllers;

use dezero\helpers\AuthHelper;
use dezero\web\Controller;
use Yii;

class AccountController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function beforeAction($action)
    {
        // Permissions
        $this->requireSuperadmin();

        $this->enableCsrfValidation = false;
        return parent::beforeAction($action);
    }


    /**
     * Main action
     */
    public function actionIndex()
    {
        // Testing AuthHelper class - Test items from RBAC system
        $this->testAuthHelper();

        // Testing AuthTrait class
        $this->testAuthTrail();

        return $this->render('//test/test/index');
    }


    /**
     * Testing AuthHelper class - Test items from RBAC system
     */
    private function testAuthHelper()
    {
        d("----------- ROLES -----------");

        // Create a new role
        d(AuthHelper::createRole('test', 'Role for testing purposes'));
        d(AuthHelper::getRoles());


        d("----------- PERMISSIONS -----------");

        // Create a new permission
        d(AuthHelper::createPermission('test_manage', 'Test - Full access'));
        d(AuthHelper::getPermissions());


        d("----------- ASSIGN PERMISSIONS TO ROLE -----------");
        d(AuthHelper::assignPermissionToRole('test', 'test_manage'));
        d(AuthHelper::getPermissionsByRole('test'));
        d(AuthHelper::revokePermissionToRole('test', 'test_manage'));
        d(AuthHelper::getPermissionsByRole('test'));

        d("----------- ASSIGN ROLE TO USER -----------");
        d(AuthHelper::assignRole('test', 1));
        d(AuthHelper::getRolesByUser(1));
        d(AuthHelper::revokeRole('test', 1));
        d(AuthHelper::getRolesByUser(1));

        d("----------- ASSIGN PERMISSIONS TO USER -----------");
        d(AuthHelper::assignPermission('test_manage', 1));
        d(AuthHelper::getPermissionsByUser(1));
        d(AuthHelper::revokePermission('test_manage', 1));
        d(AuthHelper::getPermissionsByUser(1));

        // Remove a permission
        d(AuthHelper::removePermission('test_manage'));
        d(AuthHelper::getPermissions());

        // Removes a role
        d(AuthHelper::removeRole('test'));
        d(AuthHelper::getRoles());

        dd("----------- FINISHED TESTS -----------");
    }


    /**
     * Testing AuthTrait class
     */
    private function testAuthTrail()
    {
        $user_model = Yii::$app->user->getIdentity();
        d($user_model);

        d($user_model->getId());
        d(Yii::$app->user->getId());

        d($user_model->isAdmin());
        d(Yii::$app->user->isAdmin());

        // Check if role "test" exists
        $role_item = AuthHelper::getRole('test');
        if ( $role_item === null )
        {
            d(AuthHelper::createRole('test', 'Role for testing purposes'));
        }
        $permission_item = AuthHelper::getPermission('test_view');
        if ( $permission_item === null )
        {
            d(AuthHelper::createPermission('test_view', 'View permission for testing purposes'));
        }


        d("----------- ROLES & USER -----------");
        d($user_model->assignRole('test'));
        d($user_model->hasRole('test'));
        d($user_model->getRoles());
        d($user_model->removeRole('test'));

        d(Yii::$app->user->assignRole('test'));
        d(Yii::$app->user->hasRole('test'));
        d(Yii::$app->user->getRoles());
        d(Yii::$app->user->removeRole('test'));

        d("----------- PERMISSIONS & USER -----------");
        d($user_model->assignPermission('test_view'));
        d($user_model->hasPermission('test_view'));
        d($user_model->getPermissions());
        d($user_model->removePermission('test_view'));

        d(Yii::$app->user->assignPermission('test_view'));
        d(Yii::$app->user->hasPermission('test_view'));
        d(Yii::$app->user->can('test_view'));
        d(Yii::$app->user->getPermissions());
        d(Yii::$app->user->removePermission('test_view'));

        d("----------- DIFFERENCE BETWEEN hasPermission() & can() methods -----------");
        d($user_model->hasPermission('user_manage'));       // <-- Permission is given directly?
        d($user_model->can('user_manage'));                 // <-- Permission is given directly or inherated from Role?
        d(Yii::$app->user->hasPermission('user_manage'));   // <-- Permission is given directly?
        d(Yii::$app->user->can('user_manage'));             // <-- Permission is given directly or inherated from Role?

        // can() method also works with roles
        d($user_model->can('admin'));
        d(Yii::$app->user->can('admin'));


        // Finally, remove test roles & permissions
        d(AuthHelper::removePermission('test_view'));
        d(AuthHelper::removeRole('test'));

        dd("----------- FINISHED TESTS -----------");
    }
}
