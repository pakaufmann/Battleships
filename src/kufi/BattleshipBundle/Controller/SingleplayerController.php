<?php

namespace kufi\BattleshipBundle\Controller;

use kufi\BattleshipBundle\Model\GameRepository;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use kufi\BattleshipBundle\Entity\SingleplayerGame;

/**
 * 
 * @Route("", service="singleplayerController")
 *
 */
class SingleplayerController extends Controller
{
	
	private $gameRepository;
	
	public function __construct(GameRepository $gameRepository)
	{
		$this->gameRepository = $gameRepository;
	}
	
	/**
	 * 
	 * @Route("/singleplayer", name="bs_sp_newGame")
	 * @Template()
	 */
	public function newGameAction()
	{
		$game = new SingleplayerGame(1);
		$this->gameRepository->addGame($game);
		
		return array("game" => $game);
	}
}