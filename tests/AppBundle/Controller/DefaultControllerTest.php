<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{
    private $client;
    public function __construct() {
        $this->client = static::createClient();
    }

    public function testIndex()
    {
//        $client = static::createClient();
        $crawler = $this->client->request('GET', '/');
        $this->client->followRedirects(true);

        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Login', $crawler->filter('#myModalLabel')->text());

        /**
         * test login form
         */
        $form = $crawler->selectButton('login_submit')->form();
        $form['email'] = 'info@mail.com';
        $form['password'] = 'admin';

        $crawler = $this->client->submit($form);

        $this->assertContains('info@mail.com', $form->get("email")->getValue());
        $this->assertContains('admin', $form->get("password")->getValue());

    }



    public function testRegister()
    {
//        $client = static::createClient();
        $crawler = $this->client->request('GET', '/register');
        $this->client->followRedirects(true);


        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertContains('Register', $crawler->filter('.page-header h1')->text());

        /**
         * test Registration form
         */
        $form = $crawler->selectButton('register_submit')->form();
        $form['username'] = 'islam';
        $form['email'] = 'islam@mail.com';
        $form['password'] = 'secretpassword';
        $form['mobile'] = '0123456789';

        $crawler = $this->client->submit($form);

        $this->assertContains('islam', $form->get("username")->getValue());
        $this->assertContains('islam@mail.com', $form->get("email")->getValue());
        $this->assertTrue($this->client->getResponse()->isSuccessful());


   /*
        $form->setValues(array(
            'username' => 'islam33',
            'email'  => "islam@mail.com",
            'password'  => "secretpassword",
            'mobile'  => "0123456789",
        ));
        $values = $form->getPhpValues();
        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());
    */

    }

}
