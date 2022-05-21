<?php

namespace App\Entity\Challenge;

use App\Helpers\Id;

use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\Entity\dateTrait;
use App\Helpers\Entity\madeByTrait;
use App\Helpers\Entity\dateInterface;
use App\Helpers\Entity\playerTrait;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * 
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\Table(name="Notification") 
 */
class Notification implements dateInterface{
  use Id, dateTrait, madeByTrait, playerTrait;

  /**
   * @var string
   * @ORM\Column(name="Message", type="string", length=150)
   * @Assert\NotBlank(groups={"Notification"})
   * @Assert\Length(min=3, max=150, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Notification"})
   * @SWG\Property(example="Player sign with new sponsor")  
   * @Groups({"Notification"})
   *
   */
  private $message;

  /**
   * @var Player
   * @ORM\ManyToOne(targetEntity="App\Entity\Challenge\Player", inversedBy="notifications", cascade={"persist"})
   * @ORM\JoinColumn(name="PlayerId", referencedColumnName="Id", onDelete="CASCADE")
   * @Groups({"Notification"})
   */
  private $player;

  /**
   * 
   * @var User
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notificationsCreated", cascade={"persist"})
   * @ORM\JoinColumn(name="MadeByUserId", referencedColumnName="id", onDelete="SET NULL", nullable = true)
   * @Groups({"Notification"})
   * 
   */
  private $madeBy;


  /**
   * Get the value of message
   *
   * @return  string
   */
  public function getMessage(){
    return $this->message;
  }

  /**
   * Set the value of message
   *
   * @param  string  $message
   *
   * @return  self
   */
  public function setMessage(string $message){
    $this->message = $message;

    return $this;
  }

  
}
