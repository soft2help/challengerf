<?php
namespace App\Entity\Challenge;

use DateTime;
use App\Helpers\Id;
use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\Entity\dateTrait;
use App\Helpers\Entity\madeByTrait;
use App\Helpers\Entity\updateTrait;
use App\Helpers\Entity\dateInterface;
use App\Entity\Challenge\Subscription;
use App\Helpers\Entity\updatedByTrait;
use App\Helpers\Entity\madeByInterface;
use App\Helpers\Entity\updateInterface;
use App\Helpers\Entity\updatedByInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Entity(repositoryClass="App\Repository\Challenge\PlayerRepository")
 * @ORM\Table(name="Player") 
 */
class Player implements dateInterface, updateInterface, madeByInterface, updatedByInterface{
    use Id, dateTrait, updateTrait, madeByTrait, updatedByTrait;

    /**
     * @var string
     * @ORM\Column(name="Name", type="string", length=150)
     * @Assert\NotBlank(groups={"Player"})
     * @Assert\Length(min=3, max=150, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Player"})
     * @SWG\Property(example="Tiago")  
     * @Groups({"Player"})
     */
    private $name;


    /**
     * @var int
     * @ORM\Column(name="Number", type="integer", nullable=true)
     * @SWG\Property(example=10)  
     * @Groups({"Player"})
     */
    private $number;

    /**
     * @var string
     * @ORM\Column(name="Nationality", type="string", length=150)
     * @Assert\NotBlank(groups={"Player"})
     * @Assert\Length(min=3, max=150, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Player"})
     * @SWG\Property(example="Portugal")  
     * @Groups({"Player"})
     *
     */
    private $nationality;


    /**
     * @var DateTime|null
     * @ORM\Column(name="BirthDate", type="date", nullable=true)
     * @Assert\NotNull(groups={"Player"}) 
     * @SWG\Property(example="11/02/1982", description=DATEFORMAT)  
     * @Groups({"Player"})
     * 
     */
    private $birthdate;


    /**
     * @var int
     * @SWG\Property(example=24, description="Age is calculated from birthdate")  
     * @Groups({"Player"})
     */
    private $age;


    /**
     * @var string
     * @ORM\Column(name="Position", type="string", length=3, nullable=true)
     * @Assert\NotNull(groups={"Player"}) 
     * @SWG\Property(example="G", description="Player position in field")
     * @Groups({"Player"})
     * 
     */
    private $position;


    /**
     * @var ArrayCollection<int,Notification>|Notification[]
     * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Notification", mappedBy="player")
     * ORM\OrderBy({"date" = "DESC"})
     */
    private $notifications;

    /**
     * 
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playersCreated", cascade={"persist"})
     * @ORM\JoinColumn(name="MadeByUserId", referencedColumnName="id", onDelete="SET NULL", nullable = true)
     * 
     */
    private $madeBy;


    /** 
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="playersUpdated", cascade={"persist"})
     * @ORM\JoinColumn(name="UpdatedByUserId", referencedColumnName="id", onDelete="SET NULL", nullable = true)
     */
    private $updatedBy;

    /**
     * 
     * @var Team
     * @ORM\ManyToOne(targetEntity="App\Entity\Challenge\Team", inversedBy="players", cascade={"persist"})
     * @ORM\JoinColumn(name="TeamId", referencedColumnName="Id", onDelete="SET NULL", nullable = true)
     * @Groups({"Player"})
     */
    private $team;

    /**
     * @var ArrayCollection<int,Subscription>|Subscription[]
     * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Subscription", mappedBy="player")
     * ORM\OrderBy({"date" = "DESC"})
     */
    private $subscriptions;

    public function __construct(){
        $this->notifications=new ArrayCollection();
        $this->subscriptions=new ArrayCollection();
    }

    public function setFromArray($player){
        $this->setName($player["name"]);
        $this->setNumber($player["number"]);
        $this->setNationality($player["nationality"]);
        if(is_a($player["birthdate"], 'DateTime')){
            $this->setBirthdate($player["birthdate"]);
        }else{
            $this->setBirthdate(new \Datetime($player["birthdate"]));
        }

        $this->setPosition($player["position"]);
        return $this;
    }
    /**
     * Get the value of name
     *
     * @return  string
     */ 
    public function getName(){
        return $this->name;
    }

    /**
     * Set the value of name
     *
     * @param  string  $name
     *
     * @return  self
     */ 
    public function setName(string $name){
        $this->name = $name;

        return $this;
    }

    /**
     * Get the value of number
     *
     * @return  integer
     */ 
    public function getNumber(){
        return $this->number;
    }

    /**
     * Set the value of number
     *
     * @param  integer  $number
     *
     * @return  self
     */ 
    public function setNumber($number){
        if(is_int($number)){
            $this->number = $number;
        }else{
            $this->number=null;
        }


        return $this;
    }

    /**
     * Get the value of nationality
     *
     * @return  string
     */ 
    public function getNationality(){
        return $this->nationality;
    }

    /**
     * Set the value of nationality
     *
     * @param  string  $nationality
     *
     * @return  self
     */ 
    public function setNationality(string $nationality){
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * Get the value of position
     *
     * @return  string
     */ 
    public function getPosition(){
        return $this->position;
    }

    /**
     * Set the value of position
     *
     * @param  string  $position
     *
     * @return  self
     */ 
    public function setPosition(string $position){
        $this->position = $position;

        return $this;
    }

    

    /**
     * Get the value of birthdate
     *
     * @return  DateTime
     */ 
    public function getBirthdate(){
        return $this->birthdate;
    }

    /**
     * Set the value of birthdate
     *
     * @param  DateTime|null  $birthdate
     *
     * @return  self
     */ 
    public function setBirthdate(?DateTime $birthdate){
        $this->birthdate = $birthdate;

        return $this;
    }


    /**
     * Get the value of age
     *
     * @return  int
     */
    public function getAge(): ?int
    {
        $today = new DateTime();
        if ($this->birthdate)
            return  $today->diff($this->birthdate)->y;

        return null;
    }



    /**
     * Get the value of notifications
     *
     * @return  ArrayCollection<int,Notification>|Notification[]
     */ 
    public function getNotifcations(){
        return $this->notifications;
    }

    /**
     * Set the value of Notifications
     *
     * @param  ArrayCollection<int,Notification>|Notification[]  $notifications
     *
     * @return  self
     */ 
    public function setNotifications($notifications){
        $this->notifications = $notifications;

        return $this;
    }

    

    /**
     * Get the value of team
     *
     * @return  Team
     */ 
    public function getTeam(){
        return $this->team;
    }

    /**
     * Set the value of team
     *
     * @param  Team  $team
     *
     * @return  self
     */ 
    public function setTeam(Team $team){
        $this->team = $team;

        return $this;
    }


    /**
     * Get the value of subscriptions
     *
     * @return  ArrayCollection<int,Subscription>|Subscription[]
     */ 
    public function getSubscriptions(){
        return $this->subscriptions;
    }

    /**
     * Set the value of subscriptions
     *
     * @param  ArrayCollection<int,Subscription>|Subscription[]  $subscriptions
     *
     * @return  self
     */ 
    public function setSubscriptions($subscriptions){
        $this->subscriptions = $subscriptions;

        return $this;
    }

    /**
     * Check if a user is subscribed to the player
     *
     * @param integer $userId
     * @return boolean
     */
    public function isSubscribed(int $userId){
        $subscribed=$this->subscriptions->filter(function(Subscription $subscription) use ($userId){
            return $subscription->getMadeBy()->getId() == $userId;
        })->first();

        if($subscribed)
            return true;

        return false;

    }
}
