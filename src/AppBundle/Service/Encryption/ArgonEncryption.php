<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 7/31/2019
 * Time: 7:16 AM
 */

namespace AppBundle\Service\Encryption;


class ArgonEncryption implements EncryptionServiceInterface
{

    public function hash(string $password)
    {
        return password_hash($password, PASSWORD_ARGON2I);
    }

    public function verify(string $password, string $hash)
    {
        return password_verify($password, $hash);
    }
}