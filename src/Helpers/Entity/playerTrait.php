<?php
namespace App\Helpers\Entity;

use App\Entity\Challenge\Player;


trait playerTrait{

    /**
     * Get the value of player
     *
     * @return  Player
     */
    public function getPlayer(){
        return $this->player;
    }

    /**
     * Set the value of player
     *
     * @param  Player  $player
     *
     * @return  self
     */
    public function setPlayer(Player $player){
        $this->player = $player;

        return $this;
    }
}
