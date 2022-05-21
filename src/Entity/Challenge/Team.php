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
use App\Helpers\Entity\updatedByTrait;
use App\Helpers\Entity\updateInterface;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Serializer\Annotation\Groups;
use  App\Helpers\Validator\Constraints as AppAssert;

use Symfony\Component\Validator\Constraints as Assert;


/**
 * 
 * @ORM\Entity(repositoryClass="App\Repository\Challenge\TeamRepository")
 * @ORM\Table(name="Team") 
 */
class Team implements dateInterface, updateInterface {
    use Id, dateTrait, updateTrait, madeByTrait, updatedByTrait;

    /**
     * @var string
     * @ORM\Column(name="Name", type="string", length=150)
     * @Assert\NotBlank(groups={"Team"})
     * @Assert\Length(min=3, max=150, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Team"})
     * @SWG\Property(example="Belenenses")  
     * @Groups({"Team", "Player"})
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(name="Acronym", type="string", length=10)
     * @Assert\NotBlank(groups={"Team"})
     * @Assert\Length(min=3, max=10, minMessage= "app.validation.minLength",  maxMessage= "app.validation.maxLength", groups={"Team"})
     * @SWG\Property(example="BFS")  
     * @Groups({"Team", "Player"})
     *
     */
    private $acronym;




    /**
     * @var ArrayCollection<int,Player>|Player[]
     * @ORM\OneToMany(targetEntity="App\Entity\Challenge\Player", mappedBy="team", cascade={"persist"})
     * ORM\OrderBy({"birthdate" = "DESC"})
     */
    private $players;


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




    public function __construct($name,$acronym){
        $this->name=$name;
        $this->acronym=$acronym;

        $this->players= new ArrayCollection();
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
     * Get the value of acronym
     *
     * @return  string
     */ 
    public function getAcronym(){
        return $this->acronym;
    }

    /**
     * Set the value of acronym
     *
     * @param  string  $acronym
     *
     * @return  self
     */ 
    public function setAcronym(string $acronym){
        $this->acronym = $acronym;

        return $this;
    }


    /**
     * Get the value of players
     *
     * @return  ArrayCollection<int,Player>|Player[]
     */ 
    public function getPlayers(){
        return $this->getPlayers;
    }

    /**
     * Set the value of players
     *
     * @param  ArrayCollection<int,Player>|Player[]  $players
     *
     * @return  self
     */ 
    public function setPlayers($players){
        $this->players = $players;

        return $this;
    }

    private function findPlayerCriteria(Player $checkPlayer){
        return $this->players->filter(function(Player $player) use($checkPlayer){
            return ($player->getName()==$checkPlayer->getName()) 
                    && 
                    ($player->getBirthdate()==$checkPlayer->getBirthdate()) 
                    && 
                    ($player->getNationality()==$checkPlayer->getNationality()); 
        })->first();

    }

    public function addPlayer(Player $player){
        $player->setTeam($this);
        /** @var Player $playerExists */
        $playerExists=$this->findPlayerCriteria($player);

        if (!$playerExists) {
            $this->players[] = $player;
        }else{
            $playerExists->setNumber($player->getNumber());
            $playerExists->setPosition($player->getPosition());
        }

        return $this;
    }
}
