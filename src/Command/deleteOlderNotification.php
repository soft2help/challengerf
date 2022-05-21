<?php

namespace App\Command;
use App\EventListener\MyRequestListener;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use App\Repository\DoctrineNotificationRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class deleteOlderNotification extends Command{
    use generadorTrait;

    private $notificationRepository;

    public function __construct(DoctrineNotificationRepository $notificationRepository){
        $this->notificationRepository=$notificationRepository;
        parent::__construct();
    }

    protected function configure(){
        $this->setName('app:delete:notifications')
            ->setDescription('Delete notifications older than one week')
            ->setHelp('Delete notifications older than one week');
    }

    protected function execute(InputInterface $input, OutputInterface $output){   
        $this->setIO($input, $output);
        $this->notificationRepository->deleteOlderNotications();
        $this->io->section("Notifications older than one week was deleted");
    }
}
