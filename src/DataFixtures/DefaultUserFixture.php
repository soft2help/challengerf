<?php
namespace App\DataFixtures;


use Doctrine\Persistence\ObjectManager;
use App\DataFixtures\Traits\UsuarioTrait;
use Symfony\Component\Console\Output\ConsoleOutput;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;

class DefaultUserFixture extends BaseFixture implements FixtureGroupInterface{
    use UsuarioTrait;

    public function loadData(ObjectManager $manager){
        $roleUsuario='ROLE_USER';

        $usuario=$this->genUsuario($roleUsuario,'user@mail.com', 'Fevr2022_');
       

        echo "Usuario: {$usuario->getUsername()}".PHP_EOL;
        echo "Password: {$usuario->getPlainPassword()}".PHP_EOL;
        
        $manager->persist($usuario);
        $manager->flush();
    }

    public static function getGroups(): array{
        return ['defaultuser'];
    }


    
    
}
