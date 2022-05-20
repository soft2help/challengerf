<?php

namespace App\DataFixtures\Traits;

use Faker\Factory;
use Faker\Generator;
use FOS\UserBundle\Model\UserManagerInterface;

trait BaseGeneratorTrait{
    /** @var ObjectManager */
    public $manager;

    /** @var Generator */
    public $faker;
    
    public $fileStorage;
    
    /** @var UserManagerInterface */
    public $userManager;


    public function startGenerator(){
        $this->faker = Factory::create('es_ES');
        $this->fileStorage=$this->get("files.storage");
        $this->userManager= $this->get("fos_user.user_manager.default");
        $this->manager=$this->getManager();
    }

    public function static(string $nombre){
        return array_keys($this->params->get("static.{$nombre}"));
    }




}