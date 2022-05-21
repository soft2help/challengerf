<?php
namespace App\Helpers\Tests;

use Doctrine\ORM\EntityManager;
use App\Entity\Challenge\Player;
use App\Entity\Challenge\Notification;
use Symfony\Component\BrowserKit\Cookie;
use App\Repository\NotificationRepository;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

trait TestsTrait{
    private $client = null;

    /**
     * @var ConsoleOutput
     */    
    private $output;

    /**
     * @var FormatterHelper
     */
    private $formatterHelper;

    public function setUp():void{
        $this->client = static::createClient();
    }

    private function logIn($role){
        $session = $this->client->getContainer()->get('session');

        $firewallName = 'main';
        // if you don't define multiple connected firewalls, the context defaults to the firewall name
        // See https://symfony.com/doc/current/reference/configuration/security.html#firewall-context
        $firewallContext = 'secured_area';
        $sessionName=$this->client->getContainer()->getParameter("session.storage.options")["name"];
        // you may need to use a different token class depending on your application.
        // for example, when using Guard authentication you must instantiate PostAuthenticationGuardToken
        $token = new UsernamePasswordToken('user', null, $firewallName, array($role));
      
        $session->set('_security_'.$firewallContext, serialize($token));
        $session->setName($sessionName);
        $session->save();
       
        $cookie = new Cookie($session->getName(), $session->getId());
        $this->client->getCookieJar()->set($cookie);
    }

    private function getStatusCode(){
        return $this->client->getResponse()->getStatusCode();
    }
    
    /** 
     * @return EntityManager 
     */
    private function getDoctrineManager(){
        return $this->client->getContainer()->get('doctrine')->getManager();
    }

    /** 
     * @return NotificationRepository 
     */
    private function getNotificationRepository(){
        $this->entityManager = $this->getDoctrineManager();
        
        return $this->entityManager->getRepository(Notification::class);
    }

    /** 
     * @return PlayerRepository 
     */
    private function getPlayerRepository(){
        $this->entityManager = $this->getDoctrineManager();
        
        return $this->entityManager->getRepository(Player::class);

    }

    private function loginSuperAdmin(){
        $this->logIn("ROLE_SUPER_ADMIN");
    }

    private function loginUser(){
        $this->logIn("ROLE_USER");
    }

    private function getArrayFromJson(){
        $body=$this->client->getResponse()->getContent();
        return json_decode($body,true);
    }

    private function printDebug($string){
        if ($this->output === null)
            $this->output = new ConsoleOutput();
        
        $this->output->writeln($string);
    }

    /**
     * Print a debugging message out in a big red block
     *
     * @param $string
     */
    private function printErrorBlock($string){
        if ($this->formatterHelper === null) {
            $this->formatterHelper = new FormatterHelper();
        }
        $output = $this->formatterHelper->formatBlock($string, 'bg=red;fg=white', true);
        $this->printDebug($output);
    }
}
