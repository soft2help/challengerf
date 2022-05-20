<?php
namespace App\EventListener;

use ErrorException;
use App\Helpers\Exceptions\GeneralException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use App\Helpers\Exceptions\FormValidationException;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;


class ExceptionListener{
    private $router;
    /** @var Session */
	private $session;
    public function __construct( RouterInterface $router, SessionInterface $session){
		$this->router  = $router;
        $this->session = $session;
	}

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(ExceptionEvent $event){
        $exception = $event->getThrowable();
        $request   = $event->getRequest();

        $requestJson='json'==strtolower($request->getContentType());
        
        // var_dump();
        // die();
        $api=false;
        if(substr($request->getPathInfo(), 0, 4 ) === "/api")
            $api=true;

       

        if(!$api && !(in_array("application/json",$request->getAcceptableContentTypes()) || $requestJson)){

            if($exception instanceof AccessDeniedHttpException){
                $this->session->getFlashBag()->add("error",$exception->getMessage());
                $event->setResponse(new RedirectResponse($this->router->generate("fos_user_security_logout")));
            }

            return;
        }
            
        

        if($exception instanceof AccessDeniedException ){
            $event->setResponse(new Response(json_encode(["error"=>$exception->getCode(),"message"=>$exception->getMessage()]),$exception->getCode()));
            return;
        }

        if($exception instanceof AccessDeniedHttpException ){
            $event->setResponse(new Response(json_encode(["error"=>$exception->getStatusCode(),"message"=>$exception->getMessage()]),$exception->getStatusCode()));
            return;
        }
            
       /*
        if(!$requestJson)
            return;
   
        */
        if($exception instanceof FormValidationException){
            /**@var  FormValidationException $exception */
            //$exception->formValidator->getAllToResponse()
            $event->setResponse(new Response(json_encode($exception->formValidator->getAllToResponse()),$exception->getCode()));
            return;
        }

        if($exception instanceof GeneralException){
            $event->setResponse(new Response(json_encode(["error"=>$exception->getCode(),"message"=>$exception->getMessage()]),$exception->getCode()));
            return;
        }

        if($exception instanceof BadRequestHttpException){            
            $event->setResponse(new Response(json_encode(["error"=>400,"message"=>$exception->getMessage()]),400));
            return;
        }

        $code=$exception->getCode();

        

        if(!$code || $exception instanceof ErrorException){
            $code=500;
        }

        
        //$event->setResponse(new Response(json_encode(["error"=>$code,"message"=>$exception->getMessage()]),$code));

    }
    

}