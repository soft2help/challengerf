<?php
namespace App\Controller;

use App\Entity\Challenge\Player;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

trait PlayerTrait{

   

    public function getPlayerIdFromRequest(){
        /** @var HelperController $this */
        return $this->request->attributes->get("playerId");
    }
    

    public function getPlayer($playerId):Player{
        /** @var HelperController $this */
        $player=$this->getManager()->getRepository(Player::class)->find($playerId);
        if(!$player)
            throw new \Exception("Player not exists",400);

        return $player;
    }

    public function getPlayerFromRequest($request):Player{
        $playerId=$request->attributes->get("playerId");

        
        return $this->getPlayer($playerId);
    }

}