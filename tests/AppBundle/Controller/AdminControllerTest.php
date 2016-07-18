<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{

    public function testLogin()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/admincp/login');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Login', $crawler->filter('.login_form')->text());
        $client->followRedirects(true);


        $form = $crawler->selectButton('submit_login')->form(
        array(
           'username' => 'admin',
           'password' => 'admin'
        ));

        $client->submit($form);
        $response = $client->getResponse();
        $this->assertContains('Hello', $response->getContent());




    }

}
