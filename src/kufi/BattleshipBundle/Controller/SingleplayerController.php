<?php

namespace kufi\BattleshipBundle\Controller;

use kufi\BattleshipBundle\Entity\Ship1;

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
	private $fieldSize;
	private $session;
	
	public function __construct(GameRepository $gameRepository, $session, $fieldSize)
	{
		$this->gameRepository = $gameRepository;
		$this->fieldSize = $fieldSize;
		$this->session = $session;
	}
	
	/**
	 * 
	 * @Route("/singleplayer", name="bs_sp_newGame")
	 * @Template()
	 */
	public function newGameAction()
	{
		$game = $this->gameRepository->getGame($this->session->get("sp_gameId"));
		if($game === null)
		{
			$game = new SingleplayerGame(1, $this->fieldSize);
			$this->gameRepository->addGame($game);
			$this->session->set("sp_gameId", $game->getId());
		}
		
		return array("game" => $game);
	}
	
	/**
	 * set a field in the database
	 * 
	 * @Route("/singleplayer/addShip/{x}/{y}/{length}/{orientation}", name="bs_sp_setField", defaults={"_format"="json"})
	 * @Template()
	 * 
	 * @param int $x
	 * @param int $y
	 * @param int $length
	 * @param int $orientation
	 */
	public function addShipAction($x, $y, $length, $orientation) {
		$gameId = $this->session->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false");
		}
		
		$game = $this->gameRepository->getGame($gameId);
		
		//check if the ship was already set
		$ships = $game->getUser1Ships();
		$res = $ships->filter(function($ship) use ($length) {
			return $ship->getLength() == $length;
		});
		
		if(!$res->isEmpty())
		{
			return array("success" => "false");
		}
		
		//add the ship
		$ship = new Ship1($x, $y, $length, $orientation);
		$res = $game->addUser1Ship($ship);
		$this->gameRepository->updateGame($game);
		
		return array("success" => json_encode($res));
	}
	
	/**
	 * starts a game/sets the ai fields (only if not already done)
	 * @Route("/singleplayer/startGame", name="bs_sp_startGame", defaults={"_format"="json"})
	 * @Template()
	 */
	public function startGameAction()
	{
		$gameId = $this->session->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false");
		}
		
		$game = $this->gameRepository->getGame($gameId);
		
		//ai fields already set
		if($game->getUser2Ships()->count() > 0) {
			return array("success" => "true");
		}
		
		$game->setUser2FieldsAutomatically();
		$this->gameRepository->updateGame($game);
		
		return array("success" => "true");
	}
	
	/**
	 * shoot onto a field of player 2
	 * 
	 * @Route("/singleplayer/shoot/{x}/{y}", name="sp_shoot", defaults={"_format"="json"})
	 * @Template()
	 */
	public function shootAction($x, $y)
	{
		$gameId = $this->session->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false", "hit" => "false");
		}
		
		$game = $this->gameRepository->getGame($gameId);
		
		$alreadyHit = $game->checkAlreadyHitUser2($x, $y);
		
		//shoot onto the ai field
		$hit = $game->hitFieldUser2($x, $y);
		if($hit)
		{
			$hitFields = array();
		}
		else
		{
			//ai shoots and returns all hit fields
			$hitFields = $game->user2ShootAutomatically();
		}
		
		$this->gameRepository->updateGame($game);
		
		return array("success" => "true", "alreadyHit" => json_encode($alreadyHit), "hit" => json_encode($hit), "hitFields" => $hitFields);
	}
}