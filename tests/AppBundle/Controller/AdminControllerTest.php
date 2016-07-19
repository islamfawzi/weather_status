<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AdminControllerTest extends WebTestCase
{
    private $client;
    public function __construct() {
        $this->client = static::createClient();
    }

    public function testLogin()
    {
//        $client = static::createClient();
        $crawler = $this->client->request('GET', '/admincp/login');

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Login', $crawler->filter('.login_form')->text());
        $this->client->followRedirects(true);

        $form = $crawler->selectButton('submit_login')->form();
        $form['username'] = 'admin';
        $form['password'] = 'admin';

        $crawler = $this->client->submit($form);

        $this->assertContains('admin', $form->get("username")->getValue());
        $this->assertContains('admin', $form->get("password")->getValue());
        $this->assertTrue($this->client->getResponse()->isSuccessful());



    }

}
