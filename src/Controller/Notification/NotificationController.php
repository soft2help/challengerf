<?php

namespace App\Controller\Notification;



use Swagger\Annotations as SWG;

use App\Controller\User\UserTrait;
use App\Controller\HelperController;
use App\Entity\Challenge\Notification;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Component\HttpFoundation\Request;
use FOS\UserBundle\Model\UserManagerInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
use App\Controller\Notification\NotificationHelperTrait;
use Omines\DataTablesBundle\Adapter\Doctrine\ORMAdapter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Omines\DataTablesBundle\DataTableFactory;
use Omines\DataTablesBundle\DataTable;
use Omines\DataTablesBundle\Column\TextColumn;
use Omines\DataTablesBundle\Column\DateTimeColumn;
use Doctrine\ORM\QueryBuilder;
/**
 * Notification controller
 * @Route("/api/notification", name="api_notification_")
 */
class NotificationController extends HelperController{
  use NotificationHelperTrait, UserTrait;

  
  /**
   * Endpoint to return notification info from notificationId in url segment  
   * @Rest\Get("/{notificationId}", name="get")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Notification")
   * @SWG\Response(response="200",
   *               description="Return Notification object in json format",              
   *               @SWG\Schema(type="object", ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification", "Id"}))           
   * )  
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function getNotificationAction(){
    /** @var NotificationHelperTrait|HelperController $this */

    $notification = $this->getNotificationFromRequest();

    return $this->jsonResponseSerialize($notification, 200, [
      'groups' => ["Notification", "Id"]
    ]);
  }

  /**
   * Endpoint to return a list of all notifications inside database
   * @Rest\Get("s", name="all")
   * @IsGranted({"ROLE_SUPER_ADMIN"})
   * @SWG\Tag(name="Notification")
   * @SWG\Response(response="200",
   *                description="return complete list of notifications",              
   *                @SWG\Schema(
   *                    type="array",
   *                    @SWG\Items(ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification", "Id"}))
   *                )
   * )
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function getNotificationsAction(Request $request){
    $notificationRepository = $this->getNotificationRepository();

    $notifications = $notificationRepository->getNotifications();



    return $this->jsonResponseSerialize($notifications, 200, ['groups' => ["Id", "Notification"]]);
  }

  /**
   * Endpoint to add new player to database 
   * @Rest\Post("", name="new_notication")
   * 
   * @SWG\Tag(name="Notification")
   * @SWG\Parameter(
   *     name="Notification",
   *     description="object of type notification",
   *     in="body",
   *     @SWG\Schema(
   *         type="object",
   *         ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification"})      
   *     )
   * ) 
   * @SWG\Response(response="200", description="Respuesta caso se haya creado con êxito", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   * @SWG\Response(response="400", description=DEFAULTFORMERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/formErrors"))
   */
  public function newNotificationAction(Request $request, UserManagerInterface $userManager){
    /** @var HelperController|ClienteTrait $this */

    /** @var Notification $notification */
    $notification = $this->deserializeJsonContent(Notification::class);
    
    $this->getFormValidator()->validate($notification, null, ['Notification'])->throw();


    $this->getManager()->persist($notification);
    $this->getManager()->flush();

    return $this->successResponse("The notification was created",201);
  }

  /**
   * Endpoint to list notifications with datatables output
   * @Route("/list", methods={"POST"}, options={"expose"=true}, name="list")
   * @SWG\Tag(name="Notification")
   * @SWG\Response(response="200",
   *                description="return player list",              
   *                @SWG\Definition(definition="paginator",
   *                   allOf={
   *                     @SWG\Schema(ref="#/definitions/datatables"),
   *                     @SWG\Schema(
   *                       @SWG\Property(
   *                         property="data",
   *                         type="array",
   *                          @SWG\Items(ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification"}))
   *                        )
   *                     )
   *                  })
   * )
   */
  public function listAction(Request $request, DataTableFactory $dataTableFactory){
    $table = $dataTableFactory
      ->create(['pageLength' => 100])
      ->add('id', TextColumn::class, ['visible' => false, 'field' => 'n.id'])
      ->add('message', TextColumn::class, ['field' => 'n.message', 'label' => "Message", 'orderable' => true])
      ->add('player', TextColumn::class, ['label' => 'Player', 'field' => 'p.name', 'orderable' => true, 'render' => function ($value, $context) {
        
        $html = "<strong>
                  {$value}
                  </strong>";

        return $html;
      }])
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
        'query' => function (QueryBuilder $builder) {
          $builder
            ->select(['n', 'p'])
            ->from(Notification::class, 'n')
            ->leftJoin('n.player', 'p');
        }
      ])
      ->addOrderBy('date', DataTable::SORT_DESCENDING)
      ->handleRequest($request);


    if ($table->isCallback())
      return $table->getResponse();
  }

  /**
   * Endpoint to delete Notification from database 
   * @Rest\Delete("/{notificationId}", name="delete")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Notification")
   * @SWG\Response(response="200", description="Response if everything is ok", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function deleteNotificationAction(){
    /** @var NotificationHelperTrait|HelperController $this */

    $notification = $this->getNotificationFromRequest();

    $this->getManager()->remove($notification);
    $this->getManager()->flush();


    return $this->successResponse("Notification was Deleted");
  }

  /**
   * Endpoint to update notification (doesnt make sense because already sent notification)
   * @Rest\Put("/{notificationId}", name="update_notification")
   * @IsGranted("ROLE_SUPER_ADMIN")
   * @SWG\Tag(name="Notification")
   * @SWG\Parameter(
   *     name="Notification",
   *     description="Notification object of type Notification",
   *     in="body",
   *     @SWG\Schema(
   *         type="object",
   *         ref=@Model(type=App\Entity\Challenge\Notification::class, groups={"Notification"})      
   *     )
   * ) 
   * @SWG\Response(response="200", description="Response if everything is ok", @SWG\Schema(ref= "#/definitions/Success")) 
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function updateNotificationAction(Request $request){
    /** @var HelperController|NotificationHelperTrait $this */

    $notificationToUpdate = $this->getNotificationFromRequest($request);
    

    /** @var Notification $notification */
    $notification = $this->deserializeJsonContent(Notification::class, ['object_to_populate' => $notificationToUpdate]);

    $this->getFormValidator()->validate($notification, null, ['Notification'])->throw();

    $this->getManager()->persist($notification);
    $this->getManager()->flush();


    return $this->successResponse("Notification was updated with success");
  }

}
