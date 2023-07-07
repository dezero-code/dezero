<?php
/**
 * UserManager
 *
 * Component to manage users
 */

namespace dezero\modules\user\components;

use dezero\helpers\StringHelper;
use yii\base\Component;
use user\models\User;
use Yii;

class UserManager extends Component
{
    /**
     * Get user "status_type" labels
     */
    public function statusLabels()
    {
        return [
            User::STATUS_TYPE_ACTIVE    => Yii::t('user', 'Active'),
            User::STATUS_TYPE_DISABLED  => Yii::t('user', 'Disabled'),
            User::STATUS_TYPE_BANNED    => Yii::t('user', 'Banned'),
            User::STATUS_TYPE_PENDING   => Yii::t('user', 'Pending'),
            User::STATUS_TYPE_DELETED   => Yii::t('user', 'Deleted'),
        ];
    }


    /**
     * Get user "status_type" colors
     */
    public function statusColors()
    {
        return [
            User::STATUS_TYPE_ACTIVE    => 'green-800',
            User::STATUS_TYPE_DISABLED  => 'red-800',
            User::STATUS_TYPE_BANNED    => 'purple-800',
            User::STATUS_TYPE_PENDING   => 'blue-800',
            User::STATUS_TYPE_DELETED   => 'red-800',
        ];
    }


    /**
     * Generate an auth_token value
     */
    public function generateAuthToken(int $length = 32) : string
    {
        return StringHelper::randomString($length);
    }
}
