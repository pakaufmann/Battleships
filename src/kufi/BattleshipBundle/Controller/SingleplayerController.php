<?php

namespace kufi\BattleshipBundle\Controller;

use kufi\BattleshipBundle\Forms\GameOptionForm;

use Symfony\Component\Security\Core\SecurityContext;
use kufi\BattleshipBundle\Entity\Ship1;
use kufi\BattleshipBundle\Model\GameRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use kufi\BattleshipBundle\Entity\SingleplayerGame;
use kufi\BattleshipBundle\Forms;

class SingleplayerController extends Controller
{
	private $fieldSize = 10;
	
	/**
	 * forces the site to create a new game by deleting the session id
	 * 
	 * @Route("/singleplayer/new", name="bs_sp_forceNew")
	 * @Template()
	 */
	public function newGameAction()
	{
		//reset gameId
		$request = $this->getRequest();
		$request->getSession()->set("sp_gameId", "");
		
		$game = new SingleplayerGame(0, 10);
		$form = $this->createForm(new GameOptionForm($this->get("aiFactory")->getAis()), $game);
		
		if($request->getMethod() == "POST")
		{
			$form->bindRequest($request);
		
			if($form->isValid())
			{
				//add game to database
				$this->get("gameRepository")->addGame($game);
				$this->getRequest()->getSession()->set("sp_gameId", $game->getId());
				
				//update user played games
				if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY')) {
					$user = $this->get("security.context")->getToken()->getUser();
					$user->addPlayedGame();
					$this->get("userRepository")->updateUser($user);
				}
				
				return $this->redirect($this->generateUrl("bs_sp_newGame"));
			}
		}
		
		return array("form" => $form->createView());
	}
	
	/**
	 * 
	 * @Route("/singleplayer", name="bs_sp_newGame")
	 * @Template()
	 */
	public function singleplayerAction()
	{
		$game = $this->get("gameRepository")->getGame($this->getRequest()->getSession()->get("sp_gameId"));
		if($game === null)
		{
			return $this->redirect($this->generateUrl("bs_sp_forceNew"));
		}
		
		return array("game" => $game);
	}
	
	/**
	 * set a field in the database
	 * 
	 * @Route("/singleplayer/addShip/{x}/{y}/{length}/{orientation}", name="bs_sp_setField", defaults={"_format"="json"})
	 * @Template("kufiBattleshipBundle:Game:addShip.json.twig")
	 * 
	 * @param int $x
	 * @param int $y
	 * @param int $length
	 * @param int $orientation
	 */
	public function addShipAction($x, $y, $length, $orientation) {
		$gameId = $this->getRequest()->getSession()->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false");
		}
		
		$game = $this->get("gameRepository")->getGame($gameId);
		
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
		$this->get("gameRepository")->updateGame($game);
		
		return array("success" => json_encode($res));
	}
	
	/**
	 * starts a game/sets the ai fields (only if not already done)
	 * 
	 * @Route("/singleplayer/startGame", name="bs_sp_startGame", defaults={"_format"="json"})
	 * @Template()
	 * 
	 */
	public function startGameAction()
	{
		$gameId = $this->getRequest()->getSession()->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false", "userWon" => "false", "aiWon" => "false");
		}
		
		$game = $this->get("gameRepository")->getGame($gameId);
		
		//ai fields already set
		if($game->getUser2Ships()->count() == 0) {
			$game->setUser2FieldsAutomatically();
			$this->get("gameRepository")->updateGame($game);
		}
		
		return array("success" => json_encode(true),
					 "userWon" => json_encode($game->user1HasWon()),
					 "aiWon" => json_encode($game->user2HasWon()));
	}
	
	/**
	 * shoot onto a field of player 2
	 * 
	 * @Route("/singleplayer/shoot/{x}/{y}", name="sp_shoot", defaults={"_format"="json"})
	 * @Template()
	 */
	public function shootAction($x, $y)
	{
		$gameId = $this->getRequest()->getSession()->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false", "hit" => "false");
		}
		
		$game = $this->get("gameRepository")->getGame($gameId);
		
		$alreadyHit = $game->checkAlreadyHitUser2($x, $y);
		
		//shoot onto the user ai field
		$hit = $game->hitFieldUser2($x, $y);
		//check if player has won
		$userWon = $game->user1HasWon();
		
		if($hit || $userWon)
		{
			$hitFields = array();
		}
		else
		{
			//ai shoots and returns all hit fields
			$hitFields = $game->user2ShootAutomatically($this->get("aiFactory"));
		}
		//check if ai has won
		$aiWon = $game->user2HasWon();
		
		//update statistics of user if logged in
		if ($this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY') && ($userWon || $aiWon)) {
			$user = $this->get("security.context")->getToken()->getUser();
			if($userWon)
			{
				$user->addWonGame();
			}
			else
			{
				$user->addLostGame();
			}
			$this->get("userRepository")->updateUser($user);
		}
		
		$this->get("gameRepository")->updateGame($game);
		
		return array("success" => "true",
					 "alreadyHit" => json_encode($alreadyHit),
					 "hit" => json_encode($hit),
					 "hitFields" => $hitFields,
					 "userWon" => json_encode($userWon),
					 "aiWon" => json_encode($aiWon),
		);
	}
}