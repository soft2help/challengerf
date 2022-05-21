<?php
namespace App\Controller;

use App\Entity\Challenge\Player;
use App\Repository\UserRepository;
use App\Entity\Challenge\Notification;
use App\Repository\NotificationRepository;
use App\Repository\Challenge\PlayerRepository;

trait HelperTraitController{
    /**
     * return repository associated with Player Entitie
     *
     * @return PlayerRepository
     */
    public function getPlayerRepository():PlayerRepository{
        return $this->getRepository(Player::class);
    }

    /**
     * return Notification repository asociated with Notification entitie
     *
     * @return NotificationRepository
     */
    public function getNotificationRepository():NotificationRepository{
        return $this->getRepository(Notification::class);
    }

    /**
     * return user respository
     *
     * @return UserRepository
     */
    public function getUserRepository():UserRepository{
        return $this->getRepository(User::class);
    }
}