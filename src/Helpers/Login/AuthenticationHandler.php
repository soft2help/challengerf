<?php
namespace App\Helpers\Login;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class AuthenticationHandler implements AuthenticationSuccessHandlerInterface, AuthenticationFailureHandlerInterface{
	private $router;
	private $session;
	private $translator;

	/**
	 * Constructor
	 *
	 * @author 	Joe Sexton <joe@webtipblog.com>
	 * @param 	RouterInterface $router
	 * @param 	Session $session
	 */
	public function __construct( RouterInterface $router, Session $session, TranslatorInterface $translator){
		$this->router  = $router;
		$this->session = $session;
		$this->translator = $translator;
	}

	/**
	 * onAuthenticationSuccess
 	 *
	 * @author 	Joe Sexton <joe@webtipblog.com>
	 * @param 	Request $request
	 * @param 	TokenInterface $token
	 * @return 	Response
	 */
	public function onAuthenticationSuccess( Request $request, TokenInterface $token ){
		// if AJAX login
		// if ( $request->isXmlHttpRequest() ) {

		$array = array( 'success' => "El usuario se ha logueado con êxito" ); // data to return via JSON
		$response = new Response( json_encode( $array ) );
		$response->headers->set( 'Content-Type', 'application/json' );

		return $response;

		// if form login 
		// } else {

		// 	if ( $this->session->get('_security.main.target_path' ) ) {

		// 		$url = $this->session->get( '_security.main.target_path' );

		// 	} else {

		// 		$url = $this->router->generate( 'home_page' );

		// 	} // end if

		// 	return new RedirectResponse( $url );

		// }
	}

	/**
	 * onAuthenticationFailure
	 *
	 * @author 	Joe Sexton <joe@webtipblog.com>
	 * @param 	Request $request
	 * @param 	AuthenticationException $exception
	 * @return 	Response
	 */
	 public function onAuthenticationFailure( Request $request, AuthenticationException $exception ){
		// if AJAX login
		$errors=[];

		$errors["fieldErrors"][]=[
			"field"=>"_username",
			"errors"=>[$this->translator->trans($exception->getMessage())]
		  ];

		 
		
		$array = array( 'error' => 400, 'message' => $exception->getMessage() ); // data to return via JSON
		$response = new Response( json_encode( $errors ) );
		$response->headers->set( 'Content-Type', 'application/json' );

		return $response;

		
	}
}