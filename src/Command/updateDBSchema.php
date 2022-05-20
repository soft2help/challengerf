<?php

namespace App\Command;
use App\EventListener\MyRequestListener;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class updateDBSchema extends Command{
    private $requestListener;

    public function __construct(TranslatorInterface $translator,ParameterBagInterface $params){
        $this->requestListener=new MyRequestListener($translator,$params);
        parent::__construct();
    }

  


    protected function configure(){
        $this->setName('app:updateschema')
            ->setDescription('Actualiza el esquema de la base de datos.')
            ->setHelp('Actualiza el esquema de la base de datos.');
    }

    protected function execute(InputInterface $input, OutputInterface $output){   
        
        $this->requestListener->setConstants();
        $command = $this->getApplication()->find('doctrine:schema:update');

        $arguments = array(
            'command' => 'doctrine:schema:update',            
            '--force'  => true
        ); 
    
        $greetInput = new ArrayInput($arguments);
        $returnCode = $command->run($greetInput, $output);

    }
}
