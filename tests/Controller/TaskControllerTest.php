<?php

namespace App\Tests\Controller;

use App\Entity\Task;
use App\Entity\User;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TaskControllerTest
 * @package App\Tests\Controller
 * php bin/phpunit tests/Controller/TaskControllerTest.php
 */
class TaskControllerTest extends WebTestCase
{
    public function testListTasks()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/');
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('input[name=_remember_me]'));
    }

    public function testCreateTaskAnonymously()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/create');
        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('input[name=_remember_me]'));
    }

    public function testCreateTaskLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/create');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['task[title]'] = 'A faire';
        $form['task[content]'] = 'Faire les courses.';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testEditTaskAnonymously()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/edit');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditExistingTaskNotAuthorLoggedIn()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);
        $userRepository = static::$container->get(UserRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/'.$testTask->getId().'/edit');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditExistingTaskAuthorOrRoleAdminLoggedIn()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);
        $userRepository = static::$container->get(UserRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/tasks/'.$testTask->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Modifier')->form();
        $form['task[title]'] = 'A faire 2';
        $form['task[content]'] = 'Faire les courses 2.';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testEditNotExistingTask()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/0/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testToggleTaskAnonymously()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/toggle');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testToggleExistingTaskNotAuthorLoggedIn()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);
        $userRepository = static::$container->get(UserRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/'.$testTask->getId().'/toggle');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testToggleExistingTaskAuthorOrRoleAdminLoggedIn()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);
        $userRepository = static::$container->get(UserRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/'.$testTask->getId().'/toggle');

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testToggleNotExistingTask()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/0/toggle');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testDeleteTaskAnonymously()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteExistingTaskNotAuthorLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteExistingTaskAuthorOrRoleAdminLoggedIn()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['user'=>$testUser->getId()], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testDeleteNotExistingTask()
    {
        $client = static::createClient();

        $client->request('GET', '/tasks/0/delete');
        $this->assertTrue($client->getResponse()->isNotFound());
    }
}
