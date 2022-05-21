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

class newAdmin extends Command{
    use generadorTrait, UsuarioTrait;

    private $requestListener;

    public function __construct(ParameterBagInterface $params, FilesystemInterface $filesStorage, EntityManagerInterface $manager, UserManagerInterface $userManager){       
        parent::__construct();
        $this->startGenerador($params,$filesStorage,$manager,$userManager);
    }

    protected function configure(){
        $this->setName('user:admin:add')
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
            ->setDescription('Add new Admin to database')
            ->setHelp('Add new admin to database');
    }

    private function addAdmin($username,$password){
        /** @var generadorTrait $this */


        $roleUsuario='ROLE_SUPER_ADMIN';

        
        $usuario=$this->genUsuario($roleUsuario,$username, $password);

        $this->io->success("Usuario: {$usuario->getUsername()}");
        $this->io->success("Password: {$usuario->getPlainPassword()}");
        
        $this->manager->persist($usuario);
        $this->manager->flush();

        
        $this->io->success("Admin added");
    }

    protected function execute(InputInterface $input, OutputInterface $output){
        /** @var generadorTrait $this */
        
        $username=$input->getOption('username');       
        $password=$input->getOption('password'); 
             
        $this->setIO($input, $output);
        $this->addAdmin($username, $password);
    }
}
