<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class UserControllerTest
 * @package App\Tests\Controller
 * php bin/phpunit tests/Controller/UserControllerTest.php
 */
class UserControllerTest extends WebTestCase
{
    public function testListUsersAnonymously()
    {
        $client = static::createClient();

        $client->request('GET', '/users/');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testListUsersLoggedInWithRoleUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/users/');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testListUsersLoggedInWithRoleAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/users/');
        $this->assertResponseIsSuccessful();
    }

    public function testCreateUserAnonymously()
    {
        $client = static::createClient();

        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateUserLoggedInWithRoleUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/users/create');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testCreateUserLoggedInWithRoleAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Ajouter')->form();
        $username = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
        $form['user[username]'] = $username;
        $form['user[email]'] = $username.'@email.fr';
        $form['user[plainPassword][first]'] = 'azertyuiop';
        $form['user[plainPassword][second]'] = 'azertyuiop';
        $form['user_roles'] = 'ROLE_USER';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testCreateExistingUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $crawler = $client->request('GET', '/users/create');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'johndoe2';
        $form['user[email]'] = 'johndoe2@demo.com';
        $form['user[plainPassword][first]'] = 'azertyuiop';
        $form['user[plainPassword][second]'] = 'azertyuiop';
        $form['user_roles'] = 'ROLE_USER';
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('#user div ul li')->count());
    }

    public function testEditNotExistingUser()
    {
        $client = static::createClient();

        $client->request('GET', '/users/0/edit');
        $this->assertTrue($client->getResponse()->isNotFound());
    }

    public function testEditExistingUserAnonymously()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $testUser);

        $client->request('GET', '/users/'.$testUser->getId().'/edit');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditExistingUserLoggedInWithRoleUser()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe2']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $user = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $user);

        $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertResponseStatusCodeSame(401);
    }

    public function testEditExistingUserLoggedInWithRoleAdmin()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $user = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $user);

        $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertResponseIsSuccessful();
    }

    public function testEditExistingUserLoggedInWithoutPassword()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $user = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $user);

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Modifier')->form();
        $username = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
        $form['user[username]'] = $username;
        $form['user[email]'] = $username.'@email.fr';
        $form['user_roles'] = 'ROLE_USER';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testEditExistingUserLoggedInWithPassword()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $user = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $user);

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Modifier')->form();
        $username = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
        $form['user[username]'] = $username;
        $form['user[email]'] = $username.'@email.fr';
        $form['user[plainPassword][first]'] = 'azertyuiop';
        $form['user[plainPassword][second]'] = 'azertyuiop';
        $form['user_roles'] = 'ROLE_USER';
        $client->submit($form);

        $crawler = $client->followRedirect();
        $this->assertCount(1, $crawler->filter('div.alert.alert-success'));
    }

    public function testEditExistingUserLoggedInWithExistingUsernameOrEmail()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'johndoe']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $user = $userRepository->findOneBy([], ['id'=>'DESC']);
        $this->assertInstanceOf(User::class, $user);

        $crawler = $client->request('GET', '/users/'.$user->getId().'/edit');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Modifier')->form();
        $username = substr(str_shuffle('abcdefghijklmnopqrstuvwxyz'), 0, 6);
        $form['user[username]'] = 'johndoe';
        $form['user[email]'] = $username.'@email.fr';
        $form['user_roles'] = 'ROLE_USER';
        $crawler = $client->submit($form);

        $this->assertGreaterThanOrEqual(1, $crawler->filter('#user div ul li')->count());
    }
}
