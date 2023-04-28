<?php
/**
 * UserQuery query class file
 *
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\modules\user\models\query;

/**
 * ActiveQuery class for \dezero\modules\user\models\User.
 *
 * @see \dezero\modules\user\models\User
 */
class UserQuery extends \dezero\db\ActiveQuery
{
    /**
     * Filter the query by "user_id" attribute value
     */
    public function user_id(int $user_id) : self
    {
        return $this->andWhere(['user_id' => $user_id]);
    }


    /**
     * Filter the query by "username" attribute value
     */
    public function username(string $username) : self
    {
        return $this->andWhere(['username' => $username]);
    }

    /**
     * Filter the query by "email" attribute value
     */
    public function email($email)
    {
        return $this->andWhere(['email' => $email]);
    }


    /**
     * Filter the query by "username" or "email" attribute value
     */
    public function usernameOrEmail($usernameOrEmail)
    {
        return filter_var($usernameOrEmail, FILTER_VALIDATE_EMAIL)
            ? $this->email($usernameOrEmail)
            : $this->username($usernameOrEmail);
    }
}
