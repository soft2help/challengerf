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
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\NumberColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Doctrine\ORM\QueryBuilder;

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
   * return notification list associtated with player in json response ready to be processed by datatables component in frontend
   * 
   * @Route("/{playerId}/notifications", methods={"POST"}, options={"expose"=true}, name="notifications")
   * @IsGranted({"ROLE_SUPER_ADMIN"})
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200",
   *                description="return player list",              
   *                @SWG\Definition(definition="paginator",
   *                   allOf={
   *                     @SWG\Schema(ref="#/definitions/datatables"),
   *                     @SWG\Schema(
   *                       @SWG\Property(
   *                         property="data",
   *                         type="array",
   *                          @SWG\Items(ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification", "Id"}))
   *                        )
   *                     )
   *                  })
   * )
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function listNotificationFromPlayerAction(Request $request, DataTableFactory $dataTableFactory){
    header('Access-Control-Allow-Origin: *');
    $player = $this->getPlayerFromRequest();

    $table = $dataTableFactory
      ->create(['pageLength' => 100])
      ->add('id', TextColumn::class, ['visible' => false, 'field' => 'n.id'])
      ->add('message', TextColumn::class, ['field' => 'n.message', 'label' => "Message", 'orderable' => true])
      ->add('date', DateTimeColumn::class, ['field' => 'n.date', 'format' => 'd/m/Y', 'label' => 'Date Add', 'orderable' => true])
      ->add('options', TextColumn::class, ['label' => '...', 'className' => 'text-center', 'render' => function ($value, $context) {

        return "
                        
                        <button type=\"button\" class=\"btn btn-danger btn-xs font-dark deleteNotification\" data-notificationId=\"{$context->getId()}\">
                          <span class=\"fa fa-trash\"></span> 
                        </button>
                        ";
      }])
      ->createAdapter(ORMAdapter::class, [
        'entity' => Notification::class,
        'query' => function (QueryBuilder $builder) use ($player) {
          $builder
            ->select(['n', 'p'])
            ->from(Notification::class, 'n')
            ->leftJoin('n.player', 'p')
            ->andWhere($builder->expr()->eq('p.id', ':playerId'))
            ->setParameter('playerId', $player->getId());;
        }
      ])
      ->addOrderBy('date', DataTable::SORT_DESCENDING)
      ->handleRequest($request);


    if ($table->isCallback())
      return $table->getResponse();
  }

  /**
   * return player list in json response ready to be processed by datatables component in frontend
   * 
   * @Route("/list", methods={"POST"}, options={"expose"=true}, name="list")
   * @IsGranted({"ROLE_USER"})
   * @SWG\Tag(name="Player")
   * @SWG\Response(response="200",
   *                description="return player list",              
   *                @SWG\Definition(definition="paginator",
   *                   allOf={
   *                     @SWG\Schema(ref="#/definitions/datatables"),
   *                     @SWG\Schema(
   *                       @SWG\Property(
   *                         property="data",
   *                         type="array",
   *                          @SWG\Items(ref=@Model(type=App\Entity\Challenge\Player::class, groups={"Player", "Id"}))
   *                        )
   *                     )
   *                  })
   * )
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * 
   */
  public function listAction(Request $request, DataTableFactory $dataTableFactory){
    $isUser=!$this->getUsuario()->hasRole("ROLE_SUPER_ADMIN");
    
    $userId=$this->getUsuario()->getId();

    $table = $dataTableFactory
      ->create(['pageLength' => 100])
      ->add('id', TextColumn::class, ['visible' => false, 'field' => 'p.id'])
      ->add('name', TextColumn::class, ['field' => 'p.name', 'label' => "Name", 'orderable' => true])
      ->add('number', NumberColumn::class, ['label' => 'Number', 'field' => 'p.number', 'orderable' => true, 'render' => function ($value, $context) {
        if ($value == 0)
          $value = "-";

        $html = "<strong>
                          {$value}
                        </strong>";

        return $html;
      }])
      ->add('position', TextColumn::class, ['label' => 'Position', 'field' => 'p.position', 'orderable' => true])
      ->add('nationality', TextColumn::class, ['label' => 'Nationality', 'visible' => true, 'field' => 'p.nationality', 'orderable' => true])
      ->add('birthdate', DateTimeColumn::class, ['field' => 'p.birthdate', 'format' => 'd/m/Y', 'label' => 'Birthdate', 'orderable' => true])
      ->add('age', NumberColumn::class, ['field' => 'p.age', 'label' => 'Age', 'orderable' => true, 'orderField' => 'p.birthdate'])

      ->add('options', TextColumn::class, ['label' => '...', 'className' => 'text-center', 'render' => function ($value, $context) use($isUser, $userId) {
        /** @var Player $context */
        $html= "<button type=\"button\" class=\"btn btn-light btn-xs font-dark editPlayer\" data-playerId=\"{$context->getId()}\">
                  <span class=\"fa fa-edit\"></span> 
                </button>
                <button type=\"button\" class=\"btn btn-light btn-xs font-dark newNotification\" data-playerId=\"{$context->getId()}\">
                  <span class=\"fa fa-share-alt\"></span> 
                </button>
                <a type=\"button\" target=\"_blank\" href=\"/html/player/{$context->getId()}/notifications\" class=\"btn btn-light btn-xs font-dark  notificationsFromPlayer\" data-playerId=\"{$context->getId()}\">
                  <span class=\"fa fa-list\"></span> 
                </a>                
                <button type=\"button\" class=\"btn btn-danger btn-xs font-dark deletePlayer\" data-playerId=\"{$context->getId()}\">
                  <span class=\"fa fa-trash\"></span> 
                </button>";
        
        if($isUser){
          $isSubscribed=$context->isSubscribed($userId);
          $classBtn="btn-light";
          $iconBtn="fa-chain-broken";
          if($isSubscribed){
            $classBtn="btn-primary";
            $iconBtn="fa-chain";
          }


          $html="<button type=\"button\" class=\"btn {$classBtn} btn-xs font-dark subscribeUnsubscribe\" data-playerId=\"{$context->getId()}\">
                    <span class=\"fa {$iconBtn}\"></span> 
                </button>";
        }

        return $html;
      }])
      ->createAdapter(ORMAdapter::class, [
        'entity' => Player::class,
        'query' => function (QueryBuilder $builder) {
          $builder
            ->select(['p', 't'])
            ->from(Player::class, 'p')
            ->leftJoin('p.team', 't');
        }
      ])
      ->addOrderBy('name', DataTable::SORT_DESCENDING)
      ->handleRequest($request);


    if ($table->isCallback())
      return $table->getResponse();
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
