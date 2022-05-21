<?php
namespace App\EventListener;

use App\Entity\Cliente;
use App\Entity\Entrenador;
use App\Helpers\Entity\updateInterface;
use App\Helpers\Entity\updatedByInterface;
use App\Helpers\Entity\actualizacionInterface;
use App\Helpers\Entity\actualizadoPorInterface;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DoctrinePreUpdate{
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage){
        $this->tokenStorage = $tokenStorage;
    }

    public function preUpdate(LifecycleEventArgs $args){
        
        $entity = $args->getObject();

        if ($entity instanceof updatedByInterface){
            if($entity->getUpdatedBy()==null)
                $entity->setUpdatedBy($this->tokenStorage->getToken()->getUser());

        }

        if ($entity instanceof updateInterface){
            if($entity->getUpdate()==null)
                $entity->setUpdate(new \DateTime());
        }
    }
}