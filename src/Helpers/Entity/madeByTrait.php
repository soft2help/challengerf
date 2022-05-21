<?php
namespace App\Helpers\Entity;

use App\Entity\User;


trait madeByTrait{


    /**
     * Get the value of madeBy
     *
     * @return  User
     */ 
    public function getMadeBy(){
        return $this->madeBy;
    }

    /**
     * Set the value of madeBy
     *
     * @param  User|null  $madeBy
     *
     * @return  self
     */ 
    public function setMadeBy(?User $madeBy){
        $this->madeBy = $madeBy;

        return $this;
    }
}