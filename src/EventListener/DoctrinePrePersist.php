<?php
namespace App\EventListener;

use App\Entity\User;
use App\Helpers\Entity\dateInterface;
use App\Helpers\Entity\madeByInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DoctrinePrePersist{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }

    public function prePersist(LifecycleEventArgs $args){
       
        $entity = $args->getObject();
        


        if ($entity instanceof madeByInterface){
            if($entity->getMadeBy()==null 
                && 
                $this->tokenStorage->getToken() 
                && 
                $this->tokenStorage->getToken()->getUser() instanceof User){
               
                $entity->setMadeBy($this->tokenStorage->getToken()->getUser());
            }

        }


        if ($entity instanceof dateInterface) {
            if($entity->getDate()===null)
                $entity->setDate(new \DateTime());
        }

        



        
    }
}