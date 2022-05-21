<?php
namespace App\Repository;

use App\Helpers\Repository\RepositoryHelper;

class PlayerRepository extends RepositoryHelper{
    
    public function getquery(){
        $this->getEntityManager()->createQueryBuilder();
    }

    public function getPlayers(){
        return $this->createQueryBuilder('p')
                ->orderBy('p.date','DESC')                    
                ->getQuery()
                ->getResult();

    }
}