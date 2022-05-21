<?php
namespace App\Controller;


use App\Entity\User;
use ReflectionClass;
use Twig\Environment;

use App\Helpers\Uploader;
use Doctrine\ORM\EntityManager;
use App\Helpers\Encoder\JsonEncoder;
use App\Doctrine\ORM\Id\Sha1IdGenerator;


use App\EventListener\MyRequestListener;
use App\Helpers\Validator\FormValidator;
use Doctrine\Persistence\ManagerRegistry;
use League\Flysystem\FilesystemInterface;
use Symfony\Component\Validator\Validation;
use App\Helpers\Normalizer\EntityNormalizer;
use App\Helpers\Normalizer\StaticNormalizer;
use App\Helpers\Repository\RepositoryHelper;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\HttpFoundation\Request;
use App\Helpers\Normalizer\DateTimeNormalizer;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Bridge\Doctrine\PropertyInfo\DoctrineExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\Extractor\SerializerExtractor;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorMapping;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Serializer\Mapping\ClassDiscriminatorFromClassMetadata;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class HelperController extends AbstractFOSRestController{
    use HelperTraitController;
    
    /** @var ValidatorInterface $validator */
    private $validator;
    private $serializer;
    private $translator;   
    public $params;
    /** @var Request $request */
    public $request;

    private $formValidator;
    private $requestListener;
    private $manager;
    private $response;
    private $fileStorage;
    private $router;

    public function __construct(ValidatorInterface $validator, TranslatorInterface $translator,ParameterBagInterface $params,RequestStack $requestStack){
        $this->validator=$validator;
        $this->translator=$translator;
        $this->params=$params;
        $this->request=$requestStack->getCurrentRequest();
        $this->requestListener=new MyRequestListener($translator,$params);
        $this->requestListener->setConstants();
        
    }

   

    public function getParametro($name){
        try{
            return $this->params->get($name);
        }catch(\Exception $ex){
            return null;
        }
    }

    private function fieldsFromRequest(){
        $result = array();
        if(($fields=$this->request->get("campos"))){   
            $fields=explode(",",$fields);

            foreach($fields as $campo) {
                $temp = &$result;
                $campos=explode('.', $campo);
                $nCampos=count($campos);
                if($nCampos==1){
                    $temp=$campos;
                }else{
                    for($i=0;$i<$nCampos;$i++){
                        if($i==$nCampos-1){
                            $temp[]=$campos[$i];
                        }else{
                            $temp=& $temp[$campos[$i]];
                        }
                            
                        
                    }
                } 
            }

        }
        return $result;
    }
    
    


    /**
     * Devuelve el usuario que actualmente está logueado
     *
     * @return User|null
     */
    public function getUsuario():?User{
        /** @var User $user */
        $user=$this->getUser();
        

        if(!$user->getActivo())
            throw new AccessDeniedException("User is not active");

        return $user;
    }

    

    public function getFormValidator(){
        if(!$this->formValidator)
            $this->formValidator=new FormValidator($this->validator, $this->translator);

        return $this->formValidator;
    }

    public function getManager():EntityManager{
        if(!$this->manager)
            $this->manager=$this->getDoctrine()->getManager();

        return $this->manager;
    }

    public function setLocation($location){
        $this->getResponse()->headers->set("Location",$location);
    }

    public function getResponse():Response{
        if(!$this->response)
            $this->response= new Response();

        return $this->response;
    }

   /**
    * Undocumented function
    *
    * @param string $entityClass
    * @return RepositoryHelper
    */
    public function getRepository($entityClass):RepositoryHelper{
        /** @var RepositoryHelper $repository */
        $repository=$this->getDoctrine()->getRepository($entityClass);
        $repository->setRequest($this->request);
        return $repository;
    }

    

    
    /**
     * Undocumented function
     *
     * @return Serializer
     */
    public function getSerializer($handleCircularReference=false):Serializer{
      
        if(!$this->serializer){
            
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
            $doctrineExtractor = new DoctrineExtractor($this->getDoctrine()->getManager());

            //$extractor = new PropertyInfoExtractor([], [$doctrineExtractor,new SerializerExtractor($classMetadataFactory),new PhpDocExtractor(), new ReflectionExtractor()]);
            $extractor = new PropertyInfoExtractor([], [$doctrineExtractor,new PhpDocExtractor(), new ReflectionExtractor()]);
           //$objectNormalizer=new ObjectNormalizer($classMetadataFactory,null,null,new ReflectionExtractor(), $discriminator);
            $objectNormalizer=new ObjectNormalizer($classMetadataFactory,null,null,$extractor,$discriminator,null,$defaultContext);
            $normalizers = [
                            new ArrayDenormalizer(),
                            new EntityNormalizer($this->getDoctrine()->getManager(),$objectNormalizer),
                            new DateTimeNormalizer($this->translator, $objectNormalizer),                      
                            new StaticNormalizer($this->translator,$this->params,$objectNormalizer),                            
                            $objectNormalizer
                            ];

            $this->serializer = new Serializer($normalizers, $encoders);


           
        }
      
        
       
        return $this->serializer;
    }

    /**
     * 
     *
     * @param array $dataToSerialize
     * @param integer|null $statusCode
     * @return Response
     */
    public function jsonResponseSerialize($dataToSerialize,$statusCode=null,array $context=[],$handleCircularReference=false){
        
        return $this->getResponse()->setContent($this->jsonSerialize($dataToSerialize,$context,$handleCircularReference))->setStatusCode(($statusCode??200));
    }

    public function jsonSerialize($data,array $context=[],$handleCircularReference=false):string{
        if(!empty($atributes=$this->fieldsFromRequest())){
            $context[AbstractNormalizer::ATTRIBUTES]=$atributes;
        }

        return $this->getSerializer($handleCircularReference)->serialize($data,"json",$context);
    }

    public function deserializeJsonContent($entity,$context=[],$jsonPlainText=null){  
        //$context[] 
        
        //var_dump($this->request->getContent());
          
        return $this->getSerializer()->deserialize(($jsonPlainText??$this->request->getContent()),$entity,'json',$context);
    }

    public function Error($code,$msg){
        return ["code"=>$code,"message"=>$msg];
    }

    /**
     * @param string $message
     * @return Response
     */
    public function okResponse($message):Response{
        return $this->jsonResponseSerialize($this->error(200,$message),200);

    }

    /**
     * @return Response
     */
    public function successResponse($message="",$httpCode=200):Response{
        return $this->jsonResponseSerialize(["success"=>$message],$httpCode);

    }

    /**
     * Undocumented function
     *
     * @return FilesystemInterface
     */
    public function getFileStorage(){
        if(!$this->fileStorage)
            $this->fileStorage= $this->get("files.storage");

        return $this->fileStorage;
    }

    public function getRouter(){
        if(!$this->router)
            $this->router= $this->get("router");

        return $this->router;
    }
    
    /**
     * Undocumented function
     *
     * @param mixed $value
     * @param array $contraints
     * @param array $groups
     * @return ConstraintViolationListInterface
     */
    public function validate($value,$contraints=null,$groups=null){        
       return $this->validator->validate($value,$contraints,$groups);
    }

    /**
     * Get the value of request
     */ 
    public function getRequest(){
        return $this->request;
    }

    /**
     * Set the value of request
     *
     * @return  self
     */ 
    public function setRequest($request){
        $this->request = $request;

        return $this;
    }

    public static function getSubscribedServices(){
        return [
            'router' => '?'.RouterInterface::class,
            'request_stack' => '?'.RequestStack::class,
            'http_kernel' => '?'.HttpKernelInterface::class,
            'serializer' => '?'.SerializerInterface::class,
            'session' => '?'.SessionInterface::class,
            'security.authorization_checker' => '?'.AuthorizationCheckerInterface::class,
            'templating' => '?'.EngineInterface::class,
            'twig' => '?'.Environment::class,
            'doctrine' => '?'.ManagerRegistry::class,
            'form.factory' => '?'.FormFactoryInterface::class,
            'security.token_storage' => '?'.TokenStorageInterface::class,
            'security.csrf.token_manager' => '?'.CsrfTokenManagerInterface::class,
            'parameter_bag' => '?'.ContainerBagInterface::class,
            'message_bus' => '?'.MessageBusInterface::class,
            'messenger.default_bus' => '?'.MessageBusInterface::class,
            'files.storage'=>'?'.FilesystemInterface::class,
            'fos_user.user_manager.default'=>'?'.UserManagerInterface::class
        ];
    }

    /**
     * Get the value of requestListener
     */ 
    public function getRequestListener(){
        return $this->requestListener;
    }
}
