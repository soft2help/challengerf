<?php
namespace App\Controller\Player;



use App\Entity\Challenge\Player;
use App\Controller\HelperController;
use App\Helpers\Exceptions\NotFound;
use App\Controller\PlayerTrait as ControllerPlayerTrait;

trait PlayerHelperTrait{
    use ControllerPlayerTrait;

    private function getPlayerFromRequest():Player{
        /** @var HelperController|ControllerPlayerTrait $this */

        $playerId=$this->getPlayerIdFromRequest();
        //trow not found exception si la sede no existe
        return $this->getPlayer($playerId);
    }

}