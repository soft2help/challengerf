<?php
namespace App\Repository\Challenge;

use App\Helpers\Repository\RepositoryHelper;

class PlayerRepository extends RepositoryHelper{
       

    public function getPlayers(){

        return $this->createQueryBuilder('p') 
                ->getQuery()
                ->getResult();
    }


    

}