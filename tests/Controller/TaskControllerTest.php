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
        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskAnonymously()
    {
        $client = static::createClient();

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

    public function testEditExistingTask()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

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

    public function testToggleExistingTask()
    {
        $client = static::createClient();
        $taskRepository = static::$container->get(TaskRepository::class);

        $testTask = $taskRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

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

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['user'=>null], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteExistingTaskAuthorLoggedIn()
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

    public function testDeleteExistingTaskNotAuthorLoggedInWithRoleUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['user'=>null], ['id'=>'DESC']);
        $this->assertInstanceOf(Task::class, $testTask);

        $client->request('GET', '/tasks/'.$testTask->getId().'/delete');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testDeleteExistingTaskNotAuthorLoggedInWithRoleAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);
        $taskRepository = static::$container->get(TaskRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $testTask = $taskRepository->findOneBy(['user'=>null], ['id'=>'DESC']);
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
