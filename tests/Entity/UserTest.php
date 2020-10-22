<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

/**
 * Class UserTest
 * @package App\Tests
 * php bin/phpunit tests/Entity/UserTest.php
 */
class UserTest extends TestCase
{
    public function testCreateUser()
    {
        $user = new User();
        $username = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);

        $user->setUsername($username)
            ->setEmail($username.'@email.fr')
            ->setPlainPassword('azertyuiop')
            ->setRoles()
        ;

        $this->assertInstanceOf(User::class, $user);
    }

    public function testOthersUserMethods()
    {
        $user = new User();

        $roles = $user->getRoles();
        $this->assertIsArray($roles);
    }
}
