<?php
namespace App\Helpers\Console;

use App\EventListener\MyRequestListener;
use Symfony\Component\Console\Event\ConsoleCommandEvent;


class ConsoleCommandListener
{
    private $requestListener;

    public function __construct(MyRequestListener $requestListener){
        $this->requestListener = $requestListener;
    }

    public function onConsoleCommand(ConsoleCommandEvent $event){
      $this->requestListener->setConstants();
    }
}