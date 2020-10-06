<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class DefaultControllerTest
 * @package App\Tests\Controller
 * php bin/phpunit tests/Controller/DefaultControllerTest.php
 */
class DefaultControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }
}
