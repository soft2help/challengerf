<?php
namespace App\Helpers\Normalizer;

use DateTime;


use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class DateTimeNormalizer implements  NormalizerInterface, DenormalizerInterface{
    private $translator;

    public function __construct(TranslatorInterface $translator, ObjectNormalizer $objectNormalizer){
        $this->translator = $translator;       
        $this->objectNormalizer=$objectNormalizer;
        
     }

 /**
     * {@inheritdoc}
     *
     * @throws InvalidArgumentException
     */
    public function normalize($object, $format = null, array $context = []){ 
        $dateFormat = $this->translator->trans('app.dateTimeFormat');
       
        
       $formated=$object->format($dateFormat);

       if($formated == "30/11/-0001 00:00:00")
        return "";

       return $formated;
    }

    /**
     * {@inheritdoc}
     */
    public function supportsNormalization($data, $format = null){      
       return $data instanceof \DateTimeInterface;
          
    }

    /**
     * {@inheritdoc}
     * check if keys ends with date if so try to make transformation to datetime
     */
    public function denormalize($data, $class, $format = null, array $context = array()){        
        $dateFormat = $this->translator->trans('app.dateTimeFormat');
        
        if($data=="")
            return null;
        
        $now=new \DateTime("now");
        $now->setTime(0, 0);
        $nowformat=$now->format($dateFormat);
       
        $rest=substr($nowformat,strlen($data)-strlen($nowformat));              
        $value=str_pad("{$data}",strlen($nowformat),$rest,STR_PAD_RIGHT);
        
        $data = \DateTime::createFromFormat($dateFormat,"{$value}");

       
        return $data;
    }


    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null){  
       
        $interfaces = class_implements($type);
        
        return $interfaces && in_array(\DateTimeInterface::class, $interfaces); 
        //return $type=="DateTime";
          
       
    }
}