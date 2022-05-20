<?php
namespace App\EventListener;

use App\Entity\Archivo;
use App\Entity\ClienteLesionesArchivo;
use App\Entity\Persona;
use App\Entity\User;
use League\Flysystem\FilesystemInterface;
use League\Flysystem\FileNotFoundException;
use Doctrine\Persistence\Event\LifecycleEventArgs;

class RemoveFile{

    private $fileStorage;

    public function __construct(FilesystemInterface $fileStorage) {
        $this->fileStorage=$fileStorage;
    }



    public function preRemove(LifecycleEventArgs $args){
        /**
         * @var Archivo $entity
         */
        $entity = $args->getObject();
        if(!($entity instanceof User))
            $entity->storeId();

        

        try{
            
           

        }catch(FileNotFoundException $ex){
            return false;
        }


    }
}