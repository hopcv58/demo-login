<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 3/17/2018
 * Time: 2:46 PM
 */
namespace App\Supports;

class PasswordSalterAndHasher
{
    function getSaltedEmail($email)
    {
        return str_replace('@', '', strrev($email));
    }

    function getSHA256($target)
    {
        return hash('sha256', $target);
    }

    function getSaltedPassword($password, $email)
    {
        return $this->getSHA256($this->getSHA256($email) . $password);
    }

    function getStretchedPassword($password, $email)
    {
        $saltedEmail = $this->getSaltedEmail($email);
        $saltedPassword = $this->getSaltedPassword($password, $saltedEmail);
        $hashedPassword = '';
        for ($i = 0; $i <= 100; $i++) {
            $hashedPassword = $this->getSHA256($hashedPassword . $saltedPassword . $saltedEmail);
        }
        return $hashedPassword;
    }
}