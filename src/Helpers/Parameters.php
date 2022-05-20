<?php
namespace App\Helpers;

use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class Parameters{
    private $translator;   
    private $params;

    public function __construct(TranslatorInterface $translator,ParameterBagInterface $params){        
        $this->translator=$translator;
        $this->params=$params;
    }

    public function getFormatDate(){
        return $this->translator->trans("app.dateTimeFormat");
    }


}