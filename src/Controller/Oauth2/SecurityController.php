<?php

namespace App\Controller\Oauth2;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
/**
 * Class SecurityController
 * @package App\ApiBundle\Controller
 */
class SecurityController extends AbstractController{
    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function loginAction(Request $request){
        
        // Exemple :
        // http://app.localhost/app_dev.php/oauth/v2/token?client_id=3_26bsuhm9rjogcs8o48s808w8g0wsgkowws8ok8swks8s8gc4cs&client_secret=4vaem9v5vwu8kgo48k4o00wkkoc40ook4sc8s0cs048s8o0w04&grant_type=client_credentials

        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(Security::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(Security::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(Security::AUTHENTICATION_ERROR);
            $session->remove(Security::AUTHENTICATION_ERROR);
        }

        // Add the following lines
        if ($session->has('_security.target_path')) {
            if (false !== strpos($session->get('_security.target_path'), $this->generateUrl('fos_oauth_server_authorize'))) {
                $session->set('_fos_oauth_server.ensure_logout', true);
            }
        }

        return $this->render('login/autenticar.html.twig');
    }

    /**
     * @param Request $request
     */
    public function loginCheckAction(Request $request){
    }
}
