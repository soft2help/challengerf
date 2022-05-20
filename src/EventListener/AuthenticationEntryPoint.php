<?php
namespace App\EventListener;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class AuthenticationEntryPoint implements AuthenticationEntryPointInterface
{
    private $urlGenerator;
    private $session;
     
    public function __construct(UrlGeneratorInterface $urlGenerator, SessionInterface $session){
        $this->urlGenerator = $urlGenerator;
        $this->session = $session;
    }

    public function start(Request $request, AuthenticationException $authException = null): Response
    {
       
        if($request->isMethod("GET") && $request->headers->get('content-type')!="application/json")
            return new RedirectResponse($this->urlGenerator->generate('app.login.action'));

       
        return new Response(json_encode(["loginPage"=>$this->urlGenerator->generate('app.login.action'), "message"=>$authException->getMessage()]),401);
    }
}