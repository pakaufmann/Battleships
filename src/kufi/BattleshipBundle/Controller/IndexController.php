<?php

namespace kufi\BattleshipBundle\Controller;

use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class IndexController extends Controller
{
    /**
     * @Route("/", name="bs_index")
     * @Template()
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        $cookies = $request->cookies;
        
        if($cookies->has("cssStyle")) {
            $request->getSession()->set("cssStyle", $cookies->get("cssStyle"));
        }
        return array();
    }
    
    /**
     * @Route("/changeCss/{cssStyle}", name="bs_change_css", defaults={"_format"="json"})
     * @Template()
     * @param unknown_type $cssStyle
     */
    public function changeCssAction($cssStyle)
    {
        if($cssStyle != "blue" && $cssStyle != "green")
        {
            $cssStyle = "blue";
        }
        //set the current session variable
        $request = $this->getRequest();
        $request->getSession()->set("cssStyle", $cssStyle);
        
        //set the cookie
        $response = $this->render("kufiBattleshipBundle:Index:changeCss.json.twig");
        $response->headers->setCookie(new Cookie("cssStyle", $cssStyle, 0, "/"));
        
        return $response;
    }
}
