<?php
namespace App\Helpers\Entity;

use App\Entity\User;

trait updatedByTrait{


    /**
     * Get the value of updatedBy
     *
     * @return  User
     */ 
    public function getupdatedBy(){
        return $this->updatedBy;
    }

    /**
     * Set the value of updatedBy
     *
     * @param  User|null $updatedBy
     *
     * @return  self
     */ 
    public function setupdatedBy(?User $updatedBy){
        $this->updatedBy = $updatedBy;

        return $this;
    }
}