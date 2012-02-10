<?php

namespace kufi\BattleshipBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MultiplayerController extends Controller
{
	
	/**
	 * 
	 * @Route("/multiplayer/lobby", name="bs_mp_lobby")
	 * @Template()
	 */
	public function lobbyAction()
	{
		
	}
}