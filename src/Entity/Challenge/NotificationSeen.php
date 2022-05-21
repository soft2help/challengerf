<?php
namespace App\Entity;

use App\Helpers\Id;
use Swagger\Annotations as SWG;
use Doctrine\ORM\Mapping as ORM;
use App\Helpers\Entity\dateSeenTrait;
use App\Helpers\Entity\fechaLeidoTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationSeenRepository")
 * @ORM\Table(name="NotificationSeen")
 */
class NotificationSeen{
    use  Id, dateSeenTrait;

    /**
     * @var Notification
     * @ORM\ManyToOne(targetEntity="App\Entity\Challenge\Notification", inversedBy="notifications", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="NotificationId", referencedColumnName="Id", onDelete="CASCADE")
     * 
     */
    private $notification;

    /**
     * 
     * @var User
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="notificationReaded", cascade={"persist"})
     * @ORM\JoinColumn(name="SeenByUserId", referencedColumnName="id", onDelete="SET NULL", nullable = true)
     * 
     */
    private $seenBy;



}