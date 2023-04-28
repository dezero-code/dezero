<?php
/**
 * Class SecurityHelper
 *
 * @see https://github.com/2amigos/yii2-usuario/blob/1.6.1/src/User/Helper/SecurityHelper.php
 */

namespace dezero\helpers;

use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Security;

class SecurityHelper
{
    /**
     * @var Security
     */
    protected $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }


    /**
     * Generates a secure hash from a password and a random salt.
     */
    public function generatePasswordHash(string $password, ?int $cost = null) : string
    {
        return $this->security->generatePasswordHash($password, $cost);
    }


    /**
     * Generates a random string
     */
    public function generateRandomString(int $length = 32) : string
    {
        return $this->security->generateRandomString($length);
    }


    /**
     * Validate an encrypted password
     */
    public function validatePassword(string $password, string $hash) : bool
    {
        return $this->security->validatePassword($password, $hash);
    }


    /**
     * Generate a password with length requeriments
     */
    public function generatePassword(int $length, ?array $minPasswordRequirements = null) : string
    {
        $sets = [
            'lower' => 'abcdefghjkmnpqrstuvwxyz',
            'upper' => 'ABCDEFGHJKMNPQRSTUVWXYZ',
            'digit' => '123456789',
            'special' => '!#$%&*+,-.:;<=>?@_~'
        ];
        $all = '';
        $password = '';

        if ( !isset($minPasswordRequirements) )
        {
            if ( isset(Yii::$app->getModule('user')->minPasswordRequirements) )
            {
                $minPasswordRequirements = Yii::$app->getModule('user')->minPasswordRequirements;
            }
            else
            {
                $minPasswordRequirements = [
                    'lower' => 1,
                    'digit' => 1,
                    'upper' => 1,
                ];
            }
        }
        if ( isset($minPasswordRequirements['min']) && $length < $minPasswordRequirements['min'] )
        {
            $length = $minPasswordRequirements['min'];
        }

        foreach ( $sets as $setKey => $set )
        {
            if ( isset($minPasswordRequirements[$setKey]) )
            {
                for ( $i = 0; $i < $minPasswordRequirements[$setKey]; $i++ )
                {
                    $password .= $set[array_rand(str_split($set))];
                }
            }
            $all .= $set;
        }

        $passwordLength = strlen($password);
        if ( $passwordLength > $length )
        {
            throw new InvalidConfigException('The minimum length is incompatible with other minimum requirements.');
        }

        $all = str_split($all);
        for ( $i = 0; $i < $length - $passwordLength; ++$i )
        {
            $password .= $all[array_rand($all)];
        }
        $password = str_shuffle($password);

        return $password;
    }
}
