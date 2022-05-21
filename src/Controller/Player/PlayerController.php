<?php
namespace App\Controller\Player;


use Swagger\Annotations as SWG;
use App\Entity\Challenge\Player;
use App\Controller\User\UserTrait;
use App\Controller\HelperController;
use App\Entity\Challenge\Notification;
use App\Entity\Challenge\Subscription;
use Nelmio\ApiDocBundle\Annotation\Model;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * Player Controller.
 * @Route("/api/player", name="api_player_")
 */
class PlayerController extends HelperController{
  use PlayerTrait, UserTrait;

  /**
   * Return player in Json Format
   * 
   * @Rest\Get("/{playerId}", name="get")
   * @IsGranted("ROLE_USER")
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200",
   *               description="Devuelve el objecto sede para editar",              
   *               @SWG\Schema(type="object", ref=@Model(type=App\Entity\Challenge\Player::class, groups={"Player", "Id"}))           
   * )  
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function getPlayerAction(){
    /** @var PlayerTrait|HelperController $this */

    $player = $this->getPlayerFromRequest();

    return $this->jsonResponseSerialize($player, 200, ['groups' => ["Player", "Id"]]);
  }
  
  /**
   * return all players in json format
   *
   * @Rest\Get("s", name="all")
   * @IsGranted({"ROLE_USER"})
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200",
   *                description="return complete list of player",              
   *                @SWG\Schema(
   *                    type="array",
   *                    @SWG\Items(ref=@Model(type=App\Entity\Challenge\Player::class, groups={"Player", "Id"}))
   *                )
   * )
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function getPlayersAction(){
    $playerRepository = $this->getPlayerRepository();

    $players = $playerRepository->getPlayers();

    return $this->jsonResponseSerialize($players, 200, ['groups' => ["Id", "Player"]]);
  }

  /**
   * Receive player object in json format and add it at database (deserialize and validation before add it)
   * 
   * @Rest\Post("/new", name="new_player")
   * @IsGranted({"ROLE_SUPER_ADMIN"})
   * @SWG\Tag(name="Player")
   * @SWG\Parameter(
   *     name="Player",
   *     description="Receive player object in json format",
   *     in="body",
   *     @SWG\Schema(
   *         type="object",
   *         ref=@Model(type=App\Entity\Challenge\Player::class, groups={"Player"})      
   *     )
   * ) 
   * @SWG\Response(response="200", description="response in case of success", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * @SWG\Response(response="400", description=DEFAULTFORMERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/formErrors"))
   */

  public function newPlayerAction(){
    /** @var HelperController|ClienteTrait $this */

    /** @var Player $player */
    $player = $this->deserializeJsonContent(Player::class);

    $this->getFormValidator()->validate($player, null, ['Player'])->throw();

    $this->getManager()->persist($player);
    $this->getManager()->flush();

    return $this->successResponse("The player was created");
  }


  /**
   * Endpoint to delete Player
   * 
   * @Rest\Delete("/{playerId}", name="delete")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200", description="Reponse in case that everything is ok deleting player", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function deletePlayerAction(){
    /** @var PlayerTrait|HelperController $this */

    $player = $this->getPlayerFromRequest();

    $this->getManager()->remove($player);
    $this->getManager()->flush();


    return $this->successResponse("Player was Deleted");
  }

  /**
   * Update Player data in database receive object in json format   * 
   * @Rest\Put("/{playerId}", name="update_player")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Player")
   * @SWG\Parameter(
   *     name="Player",
   *     description="Receive player object in json format",
   *     in="body",
   *     @SWG\Schema(
   *         type="object",
   *         ref=@Model(type=App\Entity\Challenge\Player::class, groups={"Player", "Id"})      
   *     )
   * ) 
   * @SWG\Response(response="200", description="Response in case of  player was succefuly updated", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */

  public function updatePlayerAction(Request $request){
    /** @var HelperController|PlayerTrait $this */

    $playerToUpdate = $this->getPlayerFromRequest($request);


    /** @var Player $player */
    $player = $this->deserializeJsonContent(Player::class, ['object_to_populate' => $playerToUpdate]);

    $this->getFormValidator()->validate($player, null, ['Player'])->throw();

    $this->getManager()->persist($player);
    $this->getManager()->flush();


    return $this->successResponse("Player was updated with success");
  }

  /**
   * Send new notification for a player
   * 
   * @Rest\Post("/{playerId}/notification", name="new_player_notification")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Player")
   * @SWG\Parameter(
   *     name="Notification",
   *     description="receive notification in json format",
   *     in="body",
   *     @SWG\Schema(
   *         type="object",
   *         ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification"})      
   *     )
   * ) 
   * @SWG\Response(response="200", description="Response in case that notification was added", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * @SWG\Response(response="400", description=DEFAULTFORMERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/formErrors"))
   */

  public function newNotificationAction(Request $request){
    /** @var HelperController|PlayerTrait $this */

    $player = $this->getPlayerFromRequest($request);

    /** @var Notification $notification */
    $notification = $this->deserializeJsonContent(Notification::class);

    $this->getFormValidator()->validate($notification, null, ['Notification'])->throw();

    $notification->setPlayer($player);

    $this->getManager()->persist($notification);
    $this->getManager()->flush();

    $htmlMail = file_get_contents($_SERVER["DOCUMENT_ROOT"] . "/theme/mail/index.html");
    $htmlMail = str_replace("{host}", $this->getRequest()->getSchemeAndHttpHost(), $htmlMail);

    //dispatch event to send email to subscribed users

    return $this->successResponse("The notification was created");
  }

  /**
   * Subscribe to player notifications
   * 
   * @Rest\Put("/{playerId}/subscribe", name="subscribe_notifications")
   * @IsGranted("ROLE_USER")
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200", description="Response in that subscription was added", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * @SWG\Response(response="400", description=DEFAULTFORMERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/formErrors"))
   */
  public function newSubscriptionAction(Request $request){
    /** @var HelperController|PlayerTrait $this */
    $user=$this->getUsuario();

    
    $player = $this->getPlayerFromRequest($request);

    if(!$player->isSubscribed($user->getId())){ 
      $newSubscription=new Subscription();
      $newSubscription->setPlayer($player);
      $newSubscription->setMadeBy($user);

      $this->getManager()->persist($newSubscription);
      $this->getManager()->flush();
    }

    

    return $this->successResponse("The subscription was created");
  }

  /**
   * Subscribe to player notifications
   * 
   * @Rest\Put("/{playerId}/unsubscribe", name="unsubscribe_notifications")
   * @IsGranted("ROLE_USER")
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200", description="Response in that subscription was added", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * @SWG\Response(response="400", description=DEFAULTFORMERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/formErrors"))
   */
  public function unSubscriptionAction(Request $request){
    /** @var HelperController|PlayerTrait $this */
    $user=$this->getUsuario();
    $userId=$this->getUsuario()->getId();

    /** @var Player $player */
    $player = $this->getPlayerFromRequest($request);

    if($player->isSubscribed($user->getId())){ 
      $subscription=$player->getSubscriptions()->filter(function(Subscription $subscription) use($userId){
        return $subscription->getMadeBy()->getId() == $userId;
      })->first();

      
      $this->getManager()->remove($subscription);
      $this->getManager()->flush();

    }

    return $this->successResponse("The subscription was deleted");
  }


}
