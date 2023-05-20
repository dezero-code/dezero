<?php
/**
 * @author Fabián Ruiz <fabian@dezero.es>
 * @link http://www.dezero.es
 * @copyright Copyright &copy; 2023 Fabián Ruiz
 */

namespace dezero\web;

use Yii;

/**
 * The User component provides APIs for managing the user authentication status.
 */
class User extends \yii\web\User
{
    /**
     * @var string|array|null the URL for login when [[loginRequired()]] is called.
     */
    public $loginUrl = ['user/login'];


    /**
     * Alias of getIdentity() method
     */
    public function getModel()
    {
        return $this->getIdentity();
    }
}
