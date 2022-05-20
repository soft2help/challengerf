<?php
namespace App\Helpers\Logout;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use FOS\UserBundle\Model\UserManagerInterface;

class LogoutListener implements LogoutHandlerInterface {
    protected $userManager;
    
    public function __construct(UserManagerInterface $userManager){
        $this->userManager = $userManager;
    }
    
    public function logout(Request $request, Response $Response, TokenInterface $Token) {
       
        if ( $request->isXmlHttpRequest() ) {
           
			$array = array( 'success' => true ); // data to return via JSON
			$response = new Response( json_encode( $array ) );
			$response->headers->set( 'Content-Type', 'application/json' );

            $response->send();
            die();
		}
    }
}