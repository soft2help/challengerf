<?php

namespace App\DataFixtures;

use Faker\Factory;
use Faker\Generator;
use Doctrine\Persistence\ObjectManager;
use League\Flysystem\FilesystemInterface;
use Doctrine\Bundle\FixturesBundle\Fixture;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Routing\RouterInterface;

abstract class BaseFixture extends Fixture
{
    /** @var ObjectManager */
    public $manager;

    /** @var Generator */
    public $faker;

    public $params;

    public $fileStorage;
    
    /** @var UserManagerInterface */
    public $userManager;

    public $translator;

    public $router;


    abstract protected function loadData(ObjectManager $manager);

    public function __construct(ParameterBagInterface $params, FilesystemInterface $filesStorage, UserManagerInterface $userManager,TranslatorInterface $translator, RouterInterface $router){
        $this->params=$params;
        $this->fileStorage=$filesStorage;
        $this->userManager=$userManager;
        $this->translator=$translator;
        $this->router=$router;
    }

    public function load(ObjectManager $manager){
        $this->manager = $manager;
        $this->faker = Factory::create("ES_es");
      
        $this->loadData($manager);
    }

    public function static(string $nombre){
        return array_keys($this->params->get("static.{$nombre}"));
    }

    public function randomElementFromEntity(string $classEntity){
        return $this->faker->randomElement($this->getAllElementFromEntity($classEntity));
    }

    public function getAllElementFromEntity(string $classEntity){
        return $this->manager->getRepository($classEntity)->findAll();
    }

    protected function createMany(string $className, int $count, callable $factory){
        for ($i = 0; $i < $count; $i++) {
            $entity = new $className();
            $factory($entity, $i);

            $this->manager->persist($entity);
            // store for usage later as App\Entity\ClassName_#COUNT#
            $this->addReference($className . '_' . $i, $entity);
        }
    }

    
}
