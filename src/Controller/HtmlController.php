<?php

namespace App\Controller;


use Swagger\Annotations as SWG;
use App\Controller\User\UserTrait;
use App\Controller\HelperController;
use App\Controller\Player\PlayerTrait;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Layout controller
 * @Route("/html", name="html_")
 */
class HtmlController extends HelperController{
  use PlayerTrait, UserTrait;

  /**
   * Return HTML to list players
   * @Rest\Get("/players", name="players_dashboard")
   * @IsGranted("ROLE_USER")
   * @SWG\Tag(name="HTML")
   * @SWG\Response(response="200", description="Return endpoint for list players layout text/html") 
   * @SWG\Get(produces={"text/html"}) 
   */
  public function htmlPlayerList(){
    $response = new Response();
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/theme/challenge/players.html");
    $response->setContent($html);

    return $response;
  }

  /**
   * Return HTML to list notifications for a player
   * @Rest\Get("/player/{playerId}/notifications", name="player_notifications")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="HTML")
   * @SWG\Response(response="200", description="return endpoint layout to list notification by player  text/html") 
   * @SWG\Get(produces={"text/html"}) 
   */
  public function notificationByPlayer(){
    $player = $this->getPlayerFromRequest();

    $response = new Response();
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/theme/challenge/notificationsPlayer.html");

    $html = str_replace("{playerId}", $player->getId(), $html);
    $html = str_replace("{player}", $player->getName(), $html);

    $response->setContent($html);

    return $response;
  }


  /**
   * Return HTML to list all notifications
   * @Rest\Get("/notifications", name="notifications_dashboard")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="HTML")
   * @SWG\Response(response="200", description="return endpoint in format text/html") 
   * @SWG\Get(produces={"text/html"}) 
   */
  public function dashboard(){    
    $response = new Response();
    $html = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/theme/challenge/notifications.html");
    $response->setContent($html);

    return $response;
  }

  


}
