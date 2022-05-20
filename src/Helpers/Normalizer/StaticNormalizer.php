<?php
namespace App\Helpers\Normalizer;

use App\Entity\Ejercicio;
use DateTime;


use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class StaticNormalizer implements  NormalizerInterface{
    private $translator;
    private $params;
    private $objectNormalizer;

    public function __construct(TranslatorInterface $translator, ParameterBagInterface $params,ObjectNormalizer $objectNormalizer){
       $this->translator = $translator;
       $this->params = $params;
       $this->objectNormalizer=$objectNormalizer;
    }

 /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = []){       
        $data = $this->objectNormalizer->normalize($object, $format, $context);
       
        if(isset($context["staticNormalize"]) && $context["staticNormalize"]==false)
            return $data;
            
        /** @var StaticInterface  $object */         
        $staticProperties=$object->getStaticProperties();
        
        foreach($staticProperties as $staticPropertie){
            if(!isset($data[$staticPropertie]))
                continue;

            $key=$staticPropertie;

            

            $value=$this->params->get("static.{$key}")[$object->{"get{$staticPropertie}"}()];

           
            
                
            $value=$this->translator->trans("{$value}");       

           
                
            

            $data[$staticPropertie]=$value;
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null){       
       return $data instanceof StaticInterface;          
    }

    
}