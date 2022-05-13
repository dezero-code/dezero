<?php

namespace dezero\modules\user\models\query;

use dezero\modules\user\models\User;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\dezero\modules\user\models\User]].
 *
 * @see \dezero\modules\user\models\User
 */
class UserQuery extends ActiveQuery
{
    /**
     * @return $this
     */
    public function active()
    {
        $this->andWhere(['status' => User::STATUS_ACTIVE]);
        $this->andWhere(['<', '{{%user}}.created_at', time()]);

        return $this;
    }
}
