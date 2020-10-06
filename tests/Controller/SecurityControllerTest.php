<?php

namespace App\Tests\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class SecurityControllerTest
 * @package App\Tests\Controller
 * php bin/phpunit tests/Controller/SecurityControllerTest.php
 */
class SecurityControllerTest extends WebTestCase
{
    public function testLoginWithoutRememberMe()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'engrev';
        $form['password'] = 'azertyuiop';
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLoginWithRememberMe()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();

        $this->assertCount(1, $crawler->filter('button[type=submit]'));
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'engrev';
        $form['password'] = 'azertyuiop';
        $form['_remember_me'] = 'on';
        $client->submit($form);

        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }

    public function testLogout()
    {
        $client = static::createClient();
        $userRepository = static::$container->get(UserRepository::class);

        $testUser = $userRepository->findOneBy(['username'=>'engrev']);
        $this->assertInstanceOf(User::class, $testUser);
        $client->loginUser($testUser);

        $client->request('GET', '/logout');
        $client->followRedirect();
        $this->assertResponseIsSuccessful();
    }
}
