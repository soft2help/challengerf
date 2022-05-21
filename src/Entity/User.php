<?php
namespace App\Entity;


use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use App\EventListener\MyRequestListener;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="User")
 * 
 */
class User extends BaseUser{

  /**
   * @var int|null
   * @ORM\Id
   * @ORM\Column(type="integer", nullable=true)
   * @ORM\GeneratedValue(strategy="AUTO")
   * @SWG\Property(example=1, description="User Id")
   * @Groups({"Id"})
   */
  protected $id;

  /**
   * @var string|null
   * @Assert\NotNull(groups={"Perfil"})
   * @Assert\NotBlank(groups={"Perfil"})
   * @Assert\Length(min=3, max=100, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Perfil"})
   * @Groups({"Perfil"})
   */
  protected $username;


  /**
   * @var string|null
   * @Assert\NotNull(groups={"Perfil"})
   * @Assert\NotBlank(groups={"Perfil"})
   * @Assert\Length(min=3, max=100, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Perfil"})
   * @Assert\Email(groups={"Perfil"})
   * @Groups({"Perfil"})
   */
  protected $email;

  /**
   * @Groups({"Perfil"})
   */
  protected $lastLogin;

    
  /**
   * @var string
   * @SWG\Property(example="USER", description="Identifica el tipo de usuario")
   * @Groups({"Perfil"})
   */
  private $tipo;

  
  /**
   * @var boolean
   * @ORM\Column(name="Activo", type="boolean", nullable=false)
   * @SWG\Property(example=true, description="EL usuario se pone activo o inactivo")
   * @Groups({"User"})
   */
  private $activo=true;





  /**
   * Undocumented variable
   *
   * @var string
   * @Groups({"User"})
   */
  private $descripcion;


  /**
   * @var ArrayCollection<int,Player>|Player[]
   * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Player", mappedBy="madeBy", cascade={"persist"})
   */
  private $playersCreated;


  /**
   * @var ArrayCollection<int,Player>|Player[]
   * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Player", mappedBy="updatedBy", cascade={"persist"})
   */
  private $playersUpdated;

  /**
   * @var ArrayCollection<int,Notification>|Notification[]
   * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Notification", mappedBy="madeBy", cascade={"persist"})
   */
  private $notificationsCreated;
  
  /**
   * @var ArrayCollection<int,Subscription>|Subscription[]
   * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Subscription", mappedBy="madeBy", cascade={"persist"})
   */
  private $subscriptionsCreated;

  public function __construct(){
    parent::__construct();

    $this->playersCreated=new ArrayCollection();
    $this->playersUpdated=new ArrayCollection();

    $this->notificationsCreated=new ArrayCollection();
    $this->subscriptionsCreated=new ArrayCollection();

  }



  public function setContrasena($defaultPassword){

    $this->setSalt(md5(uniqid()));
    $this->setPlainPassword($defaultPassword);
    //echo "defaultPassword: {$defaultPassword}".PHP_EOL;

    $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);
    $password = $encoder->encodePassword($defaultPassword, $this->getSalt());
    $this->setPassword($password);
  }

    


  public function isAdministrador(){
    return in_array("ROLE_ADMIN",$this->roles);
  }


  /**
   * Get the value of tipo
   */ 
  public function getTipo(){
    if(in_array("ROLE_SUPER_ADMIN",$this->roles))
      return "SUPER_ADMIN";

    if(in_array("ROLE_USER",$this->roles))
      return "USER";

    return null;
  }

  /**
   * Set the value of tipo
   *
   * @return  self
   */ 
  public function setTipo($tipo){
    $this->tipo = $tipo;

    return $this;
  }


  /**
   * Get the value of activo
   *
   * @return  boolean
   */ 
  public function getActivo(){
    return $this->activo;
  }

  /**
   * Set the value of activo
   *
   * @param  boolean  $activo
   *
   * @return  self
   */ 
  public function setActivo(bool $activo){
    $this->activo = $activo;

    return $this;
  }

  

  /**
   * Get the value of id
   *
   * @return  int|null
   */ 
  public function getId(){
    return $this->id;
  }

  /**
   * Set the value of id
   *
   * @param  int|null  $id
   *
   * @return  self
   */ 
  public function setId(?int $id){
    $this->id = $id;

    return $this;
  }


  /**
   * Get the value of sedeActualizadas
   *
   * @return  ArrayCollection<int,Player>|Player[]
   */ 
  public function getPlayersCreated(){
    return $this->playersCreated;
  }

  /**
   * Set the value of sedeActualizadas
   *
   * @param  ArrayCollection<int,Player>|Player[]  $playersCreated
   *
   * @return  self
   */ 
  public function setPlayersCreated($playersCreated){
    $this->playersCreated = $playersCreated;

    return $this;
  }


  /**
   * Get the value of sedeActualizadas
   *
   * @return  ArrayCollection<int,Player>|Player[]
   */ 
  public function getPlayersUpdated(){
    return $this->playersUpdated;
  }

  /**
   * Set the value of sedeActualizadas
   *
   * @param  ArrayCollection<int,Player>|Player[]  $playersUpdated
   *
   * @return  self
   */ 
  public function setPlayersUpdated($playersUpdated){
    $this->playersUpdated = $playersUpdated;

    return $this;
  }


  /**
   * Get the value of notificationsCreated
   *
   * @return  ArrayCollection<int,Notification>|Notification[]
   */ 
  public function getNotificationsCreated(){
    return $this->notificationsCreated;
  }

  /**
   * Set the value of notificationsCreated
   *
   * @param  ArrayCollection<int,Player>|Player[]  $notificationsCreated
   *
   * @return  self
   */ 
  public function setNotificationsCreated($notificationsCreated){
    $this->notificationsCreated = $notificationsCreated;

    return $this;
  }

  /**
   * Get the value of subscriptionsCreated
   *
   * @return  ArrayCollection<int,Subscription>|Subscription[]
   */ 
  public function getSubscriptionsCreated(){
    return $this->subscriptionsCreated;
  }

  /**
   * Set the value of subscriptionsCreated
   *
   * @param  ArrayCollection<int,Subscription>|Subscription[]  $notificationsCreated
   *
   * @return  self
   */ 
  public function setSubscriptionsCreated($subscriptionsCreated){
    $this->subscriptionsCreated = $subscriptionsCreated;

    return $this;
  }


}