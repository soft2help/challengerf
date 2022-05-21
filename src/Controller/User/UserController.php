<?php
namespace App\Controller\User;

use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;
use App\Controller\HelperController;
use App\DataFixtures\Traits\BaseGeneratorTrait;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


/**
 * User Controller
 * @Route("/api/user", name="api_user_")
 */
class UserController extends HelperController{
  use BaseGeneratorTrait, UserTrait;

  /**
   * Endpoint that return user info of current user logged
   * 
   * @Rest\Get("/profile", name="perfil")
   * @IsGranted("ROLE_USER")
   * @SWG\Tag(name="User")
   * @SWG\Response(response="200",
   *               description="return profile of current user",              
   *               @SWG\Schema(type="object", ref=@Model(type=App\Entity\User::class, groups={"Perfil", "Id"}))           
   * )  
   * @SWG\Response(response="default", description=DEFAULTERRORDESCRIPTION, @SWG\Schema(ref= "#/definitions/Error"))
   */
  public function getPerfilAction(){
    /** @var HelperController $this */
    $user=$this->getUsuario();
    return $this->jsonResponseSerialize($user,200,['groups' => ["Perfil","Id"]]);
  }

  

  



}
