<?php
namespace App\Tests\Controller;


use App\Helpers\Tests\TestsTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NotificationControllerTest extends WebTestCase{
    use TestsTrait;

    /**
     * Test if get notifications with admin user it will be deliver 200 status code and array type
     */
    public function testGetNotificationsWithAdminUser():void{
        $this->loginSuperAdmin();
        $crawler = $this->client->request('GET', '/api/notifications');
        $players=json_decode($this->client->getResponse()->getContent(),true);
        $isArray=is_array($players);
        $this->assertSame(Response::HTTP_OK, $this->getStatusCode());
        $this->assertEquals(true,$isArray);
    }

    /**
     * Test if get notifications with user it will be give 403 forbiden because only admin can access
     */
    public function testGetNotificationsWithUser():void{
        $this->loginUser();
        $crawler = $this->client->request('GET', '/api/notifications');
        $body=$this->client->getResponse()->getContent();
       
        $this->assertSame(Response::HTTP_FORBIDDEN, $this->getStatusCode());
        
    }

    /**
     * Test if get notification with admin it will be 200 status code and a json with message key inside
     */
    public function testGetNotificationWithAdmin():void{
        $this->loginSuperAdmin();        
        $notifications=$this->getNotificationRepository()->getNotifications();        
        if(empty($notifications))
            return;

        $notificationId=$notifications[0]->getId();

        $crawler = $this->client->request('GET', "/api/notification/{$notificationId}");
        $notification=$this->getArrayFromJson();
        
        $this->assertSame(Response::HTTP_OK, $this->getStatusCode());
        $this->assertArrayHasKey("message",$notification);
    }

    /**
     * Test validation when try to add a new notification to a player, bad request 400 status code and field Error inside json response
     */
    public function testNewNotificationValidationErrors():void{
        $players=$this->getPlayerRepository()->getPlayers();
        if(empty($players))
            return;


        $newNotification=["message"=>"n", "player"=>$players[0]->getId()];
        $crawler = $this->client
        ->request('POST', "/api/notification",[],[],array('CONTENT_TYPE' => 'application/json'),\json_encode($newNotification));
        
        $response=$this->getArrayFromJson();
        
        $this->assertSame(Response::HTTP_BAD_REQUEST, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey("fieldErrors",$response);
    }

    /**
     * Test success new notification to a player, 201 created status code and json response with field success
     */
    public function testNewNotificationSuccess():void{
        $players=$this->getPlayerRepository()->getPlayers();
        if(empty($players))
            return;


        $newNotification=["message"=>"new Sponsor is comming", "player"=>$players[0]->getId()];
        $crawler = $this
                    ->client
                    ->request('POST', 
                                "/api/notification",
                                [],
                                [],
                                array('CONTENT_TYPE' => 'application/json'),
                                \json_encode($newNotification)
                            );
        
        $response=$this->getArrayFromJson();
        
        $this->assertSame(Response::HTTP_CREATED, $this->client->getResponse()->getStatusCode());
        $this->assertArrayHasKey("success",$response);
    }
}