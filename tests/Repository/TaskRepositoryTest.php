<?php

namespace App\Tests\Repository;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Liip\TestFixturesBundle\Test\FixturesTrait;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

/**
 * Class TaskRepositoryTest
 * @package App\Tests\Repository
 * php bin/phpunit tests/Repository/TaskRepositoryTest.php
 */
class TaskRepositoryTest extends KernelTestCase
{
    use FixturesTrait;

    public function testTaskFixtures()
    {
        self::bootKernel();
        $this->loadFixtures([UserFixtures::class, TaskFixtures::class]);

        $users = self::$container->get(UserRepository::class)->count([]);
        $this->assertEquals(2, $users);

        $tasks = self::$container->get(TaskRepository::class)->count([]);
        $this->assertEquals(1, $tasks);
    }
}