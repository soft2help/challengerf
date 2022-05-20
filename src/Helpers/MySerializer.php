<?php
namespace App\Helpers;


use App\Helpers\Encoder\JsonEncoder;
use App\Helpers\Normalizer\EntityNormalizer;
use App\Helpers\Normalizer\StaticNormalizer;
use Symfony\Component\Serializer\Serializer;
use App\Helpers\Normalizer\DateTimeNormalizer;
use Doctrine\Common\Annotations\AnnotationReader;
use App\Helpers\Normalizer\DateTimeFormatNormalizer;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;

use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;

use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;

use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;


class MySerializer{

    public function getSerializer($doctrineManager,$translator,$router,$params,$dateFormat=null,$handleCircularReference=false){
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $discriminator = new ClassDiscriminatorFromClassMetadata($classMetadataFactory);
       
        $encoders = [new JsonEncoder()];

        $defaultContext=[];

        if($handleCircularReference){
            $defaultContext = [
                AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => function ($object, $format, $context) {
                    return $object->getId();
                },
            ];
        }
      
        
        $objectNormalizer=new ObjectNormalizer($classMetadataFactory, null, null, null, $discriminator,null,$defaultContext);
        $doctrineExtractor = new DoctrineExtractor($doctrineManager);

        //$extractor = new PropertyInfoExtractor([], [$doctrineExtractor,new SerializerExtractor($classMetadataFactory),new PhpDocExtractor(), new ReflectionExtractor()]);
        $extractor = new PropertyInfoExtractor([], [$doctrineExtractor,new PhpDocExtractor(), new ReflectionExtractor()]);
       //$objectNormalizer=new ObjectNormalizer($classMetadataFactory,null,null,new ReflectionExtractor(), $discriminator);
        $objectNormalizer=new ObjectNormalizer($classMetadataFactory,null,null,$extractor,$discriminator,null,$defaultContext);
        
        if(!$dateFormat)
            $dateFormat=$translator->trans('app.dateTimeFormat');
        
       
        $normalizers = [
                        new ArrayDenormalizer(),
                        new EntityNormalizer($doctrineManager,$objectNormalizer),
                        new DateTimeFormatNormalizer($dateFormat, $objectNormalizer),                                               
                        new StaticNormalizer($translator,$params,$objectNormalizer),                            
                        $objectNormalizer
                        ];
        
        $serializer=new Serializer($normalizers, $encoders);
        
        return $serializer;
        
    }

}