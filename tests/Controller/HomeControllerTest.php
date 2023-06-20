<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class HomeControllerTest extends WebTestCase
{
    public function testAdminHttpBasicAuthentication()
    {
        # set HTTP Basic credentials for admin user
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_admin',
            'PHP_AUTH_PW'   => 'test',
        ]);

        # test homepage url
        $client->request('GET', '/');

        # Asserts that HTTP 200 response is sent
        $this->assertResponseIsSuccessful();

        # Asserts that homepage displays the title correctly
        $this->assertSelectorTextContains('h1', 'Home');
    }

    public function testHttpBasicAuthenticationFailure()
    {
        # test HTTP Basic bad credentials
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'john_admin',
            'PHP_AUTH_PW'   => 'wrong_password',
        ]);

        # test the homepage
        $client->request('GET', '/');

        # Asserts that HTTP 401 response is sent
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }
}