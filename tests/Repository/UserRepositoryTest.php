<?php

namespace App\Tests\Repository;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class UserRepositoryTest
 * @package App\Tests\Repository
 * php bin/phpunit tests/Repository/UserRepositoryTest.php
 */
class UserRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testUserFixtures()
    {
        self::bootKernel();
        $this->loadFixtures([UserFixtures::class]);
        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(2, $users);
    }
}