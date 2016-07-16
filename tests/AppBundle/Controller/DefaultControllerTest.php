<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testIndex()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Login', $crawler->filter('#myModalLabel')->text());

        /**
         * test login form
         */
        $form = $crawler->selectButton('login_submit')->form();
        $form['email'] = 'info@orange.com';
        $form['password'] = 'admin';
        $crawler = $client->submit($form);
       // $this->assertTrue($client->getResponse()->isSuccessful());

        $this->assertContains('info@orange.com', $form->get("email")->getValue());
        $this->assertContains('admin', $form->get("password")->getValue());


        $crawler = $client->request('GET', '/');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Hello', $crawler->filter('h1')->text());

    }



    public function testRegister()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertContains('Register', $crawler->filter('.page-header h1')->text());

        /**
         * test Registration form
         */
        $form = $crawler->selectButton('register_submit')->form();
        $form['username'] = 'islam';
        $form['email'] = 'islam@mail.com';
        $form['password'] = 'secretpassword';
        $form['mobile'] = '0123456789';

        $crawler = $client->submit($form);

        $this->assertContains('islam', $form->get("username")->getValue());
        $this->assertContains('islam@mail.com', $form->get("email")->getValue());

        $this->assertTrue($client->getResponse()->isSuccessful());


    }




}
