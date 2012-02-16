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
		if($this->session->get("sp_gameId", "") === "")
		{
			$game = new SingleplayerGame(1, $this->fieldSize);
			$this->gameRepository->addGame($game);
			$this->session->set("sp_gameId", $game->getId());
		}
		else
		{
			$game = $this->gameRepository->getGame($this->session->get("sp_gameId"));
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
	 */
	public function setFieldAction($x, $y, $length, $orientation) {
		$gameId = $this->session->get("sp_gameId", "");
		if($gameId === "") {
			return array("success" => "false");
		}
		
		$game = $this->gameRepository->getGame($gameId);
		
		$fields = $game->getUser1Fields();
		
		//loop over all fields and set the set has ship to true
		//throw an error if the field already has a ship
		for($i=0;$i<$length;$i++) {
			$coll = $fields->filter(function($field) use ($orientation, $x, $y, $i) {
				if($orientation == 1) {
					return $field->getX() == $x && ($field->getY()) == ($y + $i) && !$field->getHasShip();
				} else {
					return $field->getX() == ($x + $i) && ($field->getY()) == $y && !$field->getHasShip();
				}
			});
			
			if($coll->isEmpty()) {
				return array("success" => "false");
			} else {
				$coll->first()->setHasSHip(true);
			}
		}
		
		$this->gameRepository->updateGame($game);
		return array("success" => "true");
	}
}