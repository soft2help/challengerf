<?php
namespace App\Helpers\Normalizer;

use DateTime;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

class EntityNormalizer implements DenormalizerInterface{   
    private $em;

    public function __construct( EntityManagerInterface $em,ObjectNormalizer $objectNormalizer){
        $this->em = $em;       
        $this->objectNormalizer=$objectNormalizer;
    } 

    /**
     * {@inheritdoc}
     * check if keys ends with date if so try to make transformation to datetime
     */
    public function denormalize($data, $class, $format = null, array $context = array()){         
       return $this->em->find($class,$data);
    }


    /**
     * {@inheritdoc}
     */
    public function supportsDenormalization($data, $type, $format = null){  
        return strpos($type, 'App\\Entity\\') === 0 && (is_numeric($data) || is_string($data));
    }
}