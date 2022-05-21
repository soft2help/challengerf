<?php
namespace  App\Controller;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use FOS\UserBundle\Controller\SecurityController as SecurityControllerH;


class SecurityController extends SecurityControllerH{
    
    public function __construct(CsrfTokenManagerInterface $tokenManager = null){
        parent::__construct($tokenManager);
    }

    /**
     * @return Response
     */
    public function loginPageAction(Request $request){
        $requestJson='json'==strtolower($request->getContentType());

        if((in_array("application/json",$request->getAcceptableContentTypes()) || $requestJson)){
            return new Response(json_encode(["error"=>401,"message"=>"You should be autenticated"]),401);
        }

        $user = $this->get('security.token_storage')->getToken()->getUser();
        
        
        if($user instanceof User) {
            return new RedirectResponse('/');
        }
    
        

        return $this->loginAction($request);
    }
    
}