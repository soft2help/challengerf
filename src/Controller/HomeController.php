<?php
namespace App\Controller;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class HomeController extends HelperController{


    /**
     * @Route("/", methods={"GET","POST"}, name="home")
     * @IsGranted("ROLE_USER")
     * @SWG\Tag(name="HTML")
     * @SWG\Response(response="200", description="return endpoint in format text/html") 
     * @SWG\Get(produces={"text/html"}) 
     */
    public function homeAction(Request $request, SessionInterface $session, ParameterBagInterface $params){        
        if ($session->get('_security.secured_area.target_path')) {
            $url = $session->get('_security.secured_area.target_path');
            //return $this->redirect($url);
    
        } 
        return $this->redirectToRoute('html_players_dashboard');

    }

}