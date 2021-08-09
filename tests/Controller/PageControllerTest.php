<?php

namespace App\Tests\Controller;

use App\Service\Mailer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class PageControllerTest extends WebTestCase
{
    public function testHomePage(){
        $client = static::createClient();
        $client->request('GET', '/' );
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testH1HomePage(){
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertSelectorTextContains('h1', 'Emploi et handicap dans votre région');
    }

    public function testMailSendEmail(){
        $client = static::createClient();
        $client->enableProfiler();
        $client->request('GET', '/');
        $mailCollector = $client->getProfile()->getCollector('mailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        /** @var Mailer[] $messages */
        $messages = $mailCollector->getMessages();
        $this->assertEquals($messages[0]->getTo(), ['contact@handi-cv.com' => null]);
    }

    public function testAuthPageIsRestricted(){
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseStatusCodeSame(Response::HTTP_UNAUTHORIZED);
    }

    public function testRedirectToLogin(){
        $client = static::createClient();
        $client->request('GET', '/admin');
        $this->assertResponseRedirects('/login');
    }
}