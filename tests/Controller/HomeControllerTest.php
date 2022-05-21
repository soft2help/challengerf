<?php
namespace App\Tests\Controller;

use App\Helpers\Tests\TestsTrait;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase{
    use TestsTrait;

    /**
     * Test if when try to go home / without authentication it will be redirect to login page
     */
    public function testHomeWithNoAuthentication():void{
        $crawler = $this->client->request('GET', '/');

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertEquals('/login', $this->client->getResponse()->headers->get('location'));
    }

    /**
     * Test if when try to go home / with authentication it will be redirect to dashboard page
     */
    public function testHomeWithAuthentication():void{
        $this->loginSuperAdmin();        
        $crawler = $this->client->request('GET', '/');
        /** @var Router $router */
        $router=$this->client->getContainer()->get('router');
        $dashboardLocation=$router->generate("html_players_dashboard");

        $this->assertSame(Response::HTTP_FOUND, $this->client->getResponse()->getStatusCode());
        $this->assertEquals($dashboardLocation, $this->client->getResponse()->headers->get('location'));
    }
}