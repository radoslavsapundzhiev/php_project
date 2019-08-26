<?php
/**
 * Created by PhpStorm.
 * User: Radoslav Sapundzhiev
 * Date: 7/31/2019
 * Time: 7:14 AM
 */

namespace AppBundle\Service\Encryption;


interface EncryptionServiceInterface
{
    public function hash(string $password);
    public function verify(string $password, string $hash);
}