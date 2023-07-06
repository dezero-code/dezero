<?php
/**
 * UserManager
 *
 * Component to manage users
 */

namespace dezero\modules\user\components;

use dezero\helpers\StringHelper;
use yii\base\Component;
use Yii;

class UserManager extends ApplicationComponent
{
    /**
     * Generate an auth_token value
     */
    public function generateAuthToken(int $length = 32) : string
    {
        return StringHelper::randomString($length);
    }
}
