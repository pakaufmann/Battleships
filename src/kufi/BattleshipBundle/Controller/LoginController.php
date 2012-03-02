<?php

namespace kufi\BattleshipBundle\Controller;

use kufi\BattleshipBundle\Forms\UserLoginForm;
use kufi\BattleshipBundle\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;

class LoginController extends Controller
{	
    /**
     * @Route("/user/login", name="bs_login")
     * @Template()
     */
    public function loginAction()
    {
    	$request = $this->getRequest();
    	$session = $request->getSession();
        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        );
    }
    
    /**
     * @Route("/user/createLogin", name="bs_createLogin")
     * @Route("/user/logincheck", name="bs_logincheck")
     * @Route("/user/logout", name="bs_logout")
     * @Template()
     */
    public function createLoginAction(Request $request)
    {
    	$user = new User();
    	$userForm = $this->createForm(new UserLoginForm(), $user);
    	
    	if($request->getMethod() == "POST")
    	{
    		$userForm->bindRequest($request);
    		
    		if($userForm->isValid())
    		{
    			$encoder = $this->get("security.encoder_factory")->getEncoder($user);
    			$user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));
    			
    			$this->get("userRepository")->addUser($user);
    			return $this->redirect($this->router->generate("bs_createSuccess"));
    		}
    	}
    	
    	return array("userForm" => $userForm->createView());
    }
    
    /**
     * @Route("/user/success", name="bs_createSuccess")
     * @Template()
     */
    public function createLoginSuccessAction()
    {
    	return array();
    }
}
