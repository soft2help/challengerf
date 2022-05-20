<?php
 namespace App\Helpers\Nelmios;
 
use EXSyst\Component\Swagger\Swagger;

use Symfony\Component\HttpFoundation\RequestStack;
use Nelmio\ApiDocBundle\Describer\DescriberInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class defaultDescriber implements DescriberInterface{
    private $request;
    private $container;
    public function __construct(RequestStack  $request,ContainerInterface $container){
        $this->request=$request->getCurrentRequest();
        $this->container=$container;
    }

    public function describe(Swagger $api){
       
        $area=$this->request->attributes->get("area");
        if($area=="default")
            return;

        if(!in_array($area,$this->container->getParameter('nelmio_api_doc.areas')))
            return; 
   
        //TODO: not so manual, should check the pattern or something like that
        $paths = $api->getPaths();
        foreach ($paths as $path => $model) {
            if(strpos($path, "/api/{$area}") !== 0) 
                $paths->remove($path);
            
        }
    }
}