<?php
namespace App\Repository\Challenge;

use App\Helpers\Repository\RepositoryHelper;

class TeamRepository extends RepositoryHelper{
       

    public function getTeamByAcronym($acronym){       
        return $this->createQueryBuilder('t')               
                ->where('t.acronym = :acronym')
                ->setParameter("acronym",$acronym)
                ->getQuery()
                ->getOneOrNullResult();
    }


    

}