<?php
namespace App\Command;


use App\Command\generadorTrait;
use Doctrine\ORM\EntityManagerInterface;
use App\DataFixtures\Traits\UsuarioTrait;

use League\Flysystem\FilesystemInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class newUser extends Command{
    use generadorTrait, UsuarioTrait;

    private $requestListener;

    public function __construct(ParameterBagInterface $params, FilesystemInterface $filesStorage, EntityManagerInterface $manager, UserManagerInterface $userManager){       
        parent::__construct();
        $this->startGenerador($params,$filesStorage,$manager,$userManager);
    }

    protected function configure(){
        $this->setName('user:user:add')
            ->addOption(
                'username',
                null,
                InputOption::VALUE_REQUIRED,
                'Access Username/email',
                null
            )
            ->addOption(
                'password',
                null,
                InputOption::VALUE_REQUIRED,
                'Password to access',
                null
            )
            ->setDescription('Add new User to database')
            ->setHelp('Add new user to database');
    }

    private function addUser($username,$password){
        /** @var generadorTrait $this */


        $roleUsuario='ROLE_USER';

        
        $usuario=$this->genUsuario($roleUsuario,$username, $password);

        $this->io->success("Usuario: {$usuario->getUsername()}");
        $this->io->success("Password: {$usuario->getPlainPassword()}");
        
        $this->manager->persist($usuario);
        $this->manager->flush();

        
        
        
        
        $this->io->success("User added");
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        /** @var generadorTrait $this */
        
        $username=$input->getOption('username');       
        $password=$input->getOption('password'); 
             
        $this->setIO($input, $output);
        $this->addUser($username, $password);
    }
}
