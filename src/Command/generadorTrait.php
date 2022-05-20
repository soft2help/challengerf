<?php
namespace App\Command;


use Faker\Factory;
use App\Entity\User;
use Faker\Generator;
use Doctrine\ORM\EntityManagerInterface;
use League\Flysystem\FilesystemInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use App\Controller\HelperTraitController;

trait generadorTrait{
    use HelperTraitController;


    /** @var EntityManagerInterface */
    public $manager;

    /** @var Generator */
    public $faker;

    /** @var FilesystemInterface */
    public $fileStorage;
    
    /** @var UserManagerInterface */
    public $userManager;

    /** @var ParameterBagInterface */
    public $params;

    /** @var SymfonyStyle $io */
    public $io;
 
 
    public function startGenerador(ParameterBagInterface $params, FilesystemInterface $filesStorage, EntityManagerInterface $manager, UserManagerInterface $userManager){
        $this->params=$params;
        $this->fileStorage=$filesStorage;
        $this->faker = Factory::create('es_ES');
        $this->manager=$manager;
        $this->userManager= $userManager;
    }
 
    public function static(string $nombre){
        return array_keys($this->params->get("static.{$nombre}"));
    }

    public function getStatic($property,$key){
        return $this->params->get("static.{$property}")[$key];
    }

    public function setIO(InputInterface $input, OutputInterface $output){
        $this->io = new SymfonyStyle($input, $output);
    }
    
    public function randomElementFromEntity(string $classEntity){
        
        return $this->faker->randomElement($this->getAllElementFromEntity($classEntity));
    }

    public function getAllElementFromEntity(string $classEntity){
        return $this->manager->getRepository($classEntity)->findAll();
    }

    public function getRandomSuperAdmin(){
        /** @var User[] $users */
        $users=$this->manager->getRepository(User::class)->findAll();

        $users=new ArrayCollection($users);

        $superAdmins=$users->filter(function(User $user){
            return $user->isSuperAdmin();
        });


        return $this->faker->randomElement($superAdmins);
    }

    public function getRepository(string $classEntity){        
        return $this->manager->getRepository($classEntity);
    }


    


}