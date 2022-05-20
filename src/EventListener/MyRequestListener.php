<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

final class MyRequestListener{
    private $translator;
    private $params;

    public function __construct(TranslatorInterface $translator,ParameterBagInterface $params){
        $this->translator=$translator;
        $this->params=$params;
    }

    public function setConstants(){
        
        !defined("DATEFORMAT") && define("DATEFORMAT",$this->translator->trans("app.dateFormat"));
        !defined("DATETIMEFORMAT") && define("DATETIMEFORMAT",$this->translator->trans("app.dateTimeFormat"));
        
        !defined("DEFAULTERRORDESCRIPTION") && define("DEFAULTERRORDESCRIPTION",$this->translator->trans("app.defaultErrorResponse"));
        !defined("DEFAULTFORMERRORDESCRIPTION") && define("DEFAULTFORMERRORDESCRIPTION",$this->translator->trans("app.defaultFormErrorResponse"));
       
        
    }

    public function static($key,$subKey1=null){
        $staticKey="static.{$key}";
        if($subKey1)
            $staticKey.=".$subKey1";

        return $this->params->get($staticKey);

    }

    public function getStatic($key,$subKey1=null){
        $staticKey="static.{$key}";
        if($subKey1)
            $staticKey.=".$subKey1";

        $static=$this->params->get($staticKey);


        $lista=[];

        foreach($static as $clave=>$valor){
            $lista[]=["clave"=>$clave,"valor"=>$this->translator->trans($valor)];
        }

        return $lista;


    }

    private function getStaticExample($key){
        return implode(" | ",array_keys($this->params->get("static.{$key}")));
    }

    public function __invoke(RequestEvent $event): void{
       $this->setConstants();
        
    }
}