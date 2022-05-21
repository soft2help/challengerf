<?php
namespace App\Entity\Challenge;

use App\Helpers\Id;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\Entity\dateTrait;
use App\Helpers\Entity\madeByTrait;
use App\Helpers\Entity\dateInterface;
use App\Helpers\Entity\playerTrait;


/**
 * @ORM\Entity(repositoryClass="App\Repository\SubscriptionRepository")
 * @ORM\Table(name="Subscription") 
 */
class Subscription implements dateInterface{
  use Id, dateTrait, madeByTrait, playerTrait;

  /**
   * @var Player
   * @ORM\ManyToOne(targetEntity="App\Entity\Challenge\Player", inversedBy="subscriptions", cascade={"persist"})
   * @ORM\JoinColumn(name="PlayerId", referencedColumnName="Id", onDelete="CASCADE")
   */
  private $player;

  /**
   * @var User
   * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="subscriptionsCreated", cascade={"persist"})
   * @ORM\JoinColumn(name="MadeByUserId", referencedColumnName="id", onDelete="SET NULL", nullable = true)
   * 
   */
  private $madeBy;

}
