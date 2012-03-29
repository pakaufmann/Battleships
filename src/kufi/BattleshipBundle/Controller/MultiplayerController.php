<?php

namespace kufi\BattleshipBundle\Controller;

use kufi\BattleshipBundle\Model\GameRepository;

use kufi\BattleshipBundle\kufiBattleshipBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use kufi\BattleshipBundle\Entity\MultiplayerGame;
use kufi\BattleshipBundle\Forms\MultiplayerForm;

/**
 * 
 * @Secure(roles="IS_AUTHENTICATED_FULLY")
 *
 */
class MultiplayerController extends Controller
{
    /**
     * @Route("/multiplayer/loginNeeded", name="bs_mp_loginNeeded")
     * @Template()
     */
    public function loginNeededAction()
    {
        return array();
    }
    
	/**
	 * 
	 * @Route("/multiplayer/lobby", name="bs_mp_lobby")
	 * @Template()
	 */
	public function lobbyAction()
	{
	    if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
	    {
	        return $this->redirect($this->generateUrl("bs_mp_loginNeeded"));
	    }
	                
	    return array("openGames" => $this->get("gameRepository")->getAllOpenMultiplayerGames());
	}
	
	/**
	 * 
	 * @Route("/multiplayer/createNew", name="bs_mp_createNew")
	 * @Template()
	 */
	public function createNewGameAction()
	{
	    if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
	    {
	        return $this->redirect($this->generateUrl("bs_mp_loginNeeded"));
	    }
	    
	    $request = $this->getRequest();
	    $game = new MultiplayerGame();
	    $form = $this->createForm(new MultiplayerForm(), $game);
	    
	    if($request->getMethod() == "POST")
	    {
	        $form->bindRequest($request);
	    
	        if($form->isValid())
	        {
	            $user = $this->get("security.context")->getToken()->getUser();
	            
	            //add game to database
	            $game->setPlayerOne($user);
	            //set player 1 as starting user
	            $game->setUserTurn($user);
	            $this->get("gameRepository")->addGame($game);
	            $this->getRequest()->getSession()->set("mp_gameId", $game->getId());
	    
	            //update user played games
                $user->addPlayedGame();
                $this->get("userRepository")->updateUser($user);
	    
	            return $this->redirect($this->generateUrl("bs_mp_game"));
	        }
	    }
	    
	    return array("form" => $form->createView());
	}
	
	/**
	 * @Route("/multiplayer", name="bs_mp_game")
	 * @Template()
	 */
	public function gameAction()
	{
	    $gameId = $this->getRequest()->getSession()->get("mp_gameId");
	    $game = $this->get("gameRepository")->getGame($gameId);
	    
	    if($game == null)
	    {
	        $this->get("session")->setFlash("gameGone", "The game isn't present anymore");
	        return $this->redirect($this->generateUrl("bs_mp_lobby"));
	    }
	    
	    return array("game" => $game);
	}
	
	/**
	 * @Route("/multiplayer/join/{id}", name="bs_mp_joinGame")
	 */
	public function joinAction($id)
	{
	    if(!$this->get('security.context')->isGranted('IS_AUTHENTICATED_FULLY'))
	    {
	        return $this->redirect($this->generateUrl("bs_mp_loginNeeded"));
	    }
	    
	    $game = $this->get("gameRepository")->getGame($id);
	    
	    if($game == null)
	    {
	        $this->get("session")->setFlash("gameGone", "The game isn't present anymore");
	        return $this->redirect($this->generateUrl("bs_mp_lobby"));
	    }
	    
	    $user = $this->get("security.context")->getToken()->getUser();
	    $this->getRequest()->getSession()->set("mp_gameId", $game->getId());
	    
	    $game->setPlayerTwo($user);
	    $this->get("gameRepository")->updateGame($game);
	    
	    //update user played games
	    $user->addPlayedGame();
	    $this->get("userRepository")->updateUser($user);
	    
	    return $this->redirect($this->generateUrl("bs_mp_game"));
	}
	
	/**
	 * set a field in the database
	 *
	 * @Route("/multiplayer/addShip/{x}/{y}/{length}/{orientation}", name="bs_mp_setField", defaults={"_format"="json"})
	 * @Template("kufiBattleshipBundle:Game:addShip.json.twig")
	 *
	 * @param int $x
	 * @param int $y
	 * @param int $length
	 * @param int $orientation
	 */
	public function addShipAction($x, $y, $length, $orientation) {
	    $gameId = $this->getRequest()->getSession()->get("mp_gameId", "");
	    if($gameId === "") {
	        return array("success" => "false");
	    }
	    
	    $game = $this->get("gameRepository")->getGame($gameId);
	    if($game === null)
	    {
	        return array("success" => "false");
	    }
	    
	    $user = $this->get("security.context")->getToken()->getUser();
	    
	    //check if the ship was already set
	    $ships = $game->getShipsForUser($user);
	    $res = $ships->filter(function($ship) use ($length) {
	        return $ship->getLength() == $length;
	    });
	
	    if(!$res->isEmpty())
	    {
	        return array("success" => "false");
	    }
	
	    //add the ship
	    $res = $game->addShipForUser($user, $x, $y, $length, $orientation);
	    $this->get("gameRepository")->updateGame($game);
	
	    return array("success" => json_encode($res));
	}
	
	/**
	 * @Route("/multiplayer/startGame", name="bs_mp_checkGame", defaults={"_format"="json"})
	 * @Template()
	 */
	public function checkGameAction()
	{
	    $gameId = $this->getRequest()->getSession()->get("mp_gameId", "");
	    if($gameId === "") {
	        return array("success" => "false", "userReady" => "false", "userTurn" => "false");
	    }
	     
	    $game = $this->get("gameRepository")->getGame($gameId);
	    $user = $this->get("security.context")->getToken()->getUser();
	    
	    if($game === null) {
	        return array("success" => "false", "userReady" => "false");
	    }
	    
	    $game->setUserReady($user);
	    $this->get("gameRepository")->updateGame($game);
	    
	    return array("success" => "true", "userReady" => json_encode($game->isOppositeUserReady($user)), "userTurn" => json_encode($game->isUserTurn($user)));
	}
	
	/**
	 * shoot onto a field
	 *
	 * @Route("/multiplayer/shoot/{x}/{y}", name="mp_shoot", defaults={"_format"="json"})
	 * @Template()
	 */
	public function shootAction($x, $y)
	{
	    $user = $this->get("security.context")->getToken()->getUser();
	    
	    $gameId = $this->getRequest()->getSession()->get("mp_gameId", "");
	    if($gameId === "") {
	        return array("success" => "false", "hit" => "false", "alreadyHit" => "false", "userWon" => "false");
	    }
	
	    $game = $this->get("gameRepository")->getGame($gameId);
	    if($game === null) {
	        return array("success" => "false", "hit" => "false", "alreadyHit" => "false", "userWon" => "false");
	    }
	    
	    //not my turn
	    if(!$game->isUserTurn($user))
	    {
	        return array("success" => "false", "hit" => "false", "alreadyHit" => "false", "userWon" => "false");
	    }
	    
	    $alreadyHit = $game->checkOppositeUserAlreadyHit($x, $y, $user);
	    
	    //shoot onto the opposite user field
	    $hit = $game->hitFieldOppositeUser($x, $y, $user);
	    //check if player has won
	    $userWon = $game->userHasWon($user);
	    
	    if($hit)
	    {
	        $game->setUserTurn($user);
	    }
	    else
	    {
	        $game->setUserTurn($game->getOppositeUser($user));
	    }
	
	    //update statistics of user if logged in
	    if($userWon) {
            $user->addWonGame();
            $oppositeUser = $game->getOppositeUser($user);
            $oppositeUser->addLostGame();
            $this->get("userRepository")->updateUser($user);
            $this->get("userRepository")->updateUser($oppositeUser);
	    }
	
	    $this->get("gameRepository")->updateGame($game);
	
	    return array("success" => "true",
	            "alreadyHit" => json_encode($alreadyHit),
	            "hit" => json_encode($hit),
	            "userWon" => json_encode($userWon)
	    );
	}
	
	/**
	 * @Route("/multiplayer/checkOpposite", name="bs_mp_checkOpposite", defaults={"_format"="json"})
	 * @Template()
	 */
	public function checkOppositeAction()
	{
	    $user = $this->get("security.context")->getToken()->getUser();
	     
	    $gameId = $this->getRequest()->getSession()->get("mp_gameId", "");
	    if($gameId === "") {
	        return array("playerTurn" => "false", "hitFields" => array(), "oppositeWon" => "false", "userWon" => "false");
	    }
	    
	    $game = $this->get("gameRepository")->getGame($gameId);
	    if($game === null) {
	        return array("playerTurn" => "false", "hitFields" => array(), "oppositeWon" => "false", "userWon" => "false");
	    }
	    
	    return array("playerTurn" => json_encode($game->isUserTurn($user)),
	                 "hitFields" => $game->getShotFields($user),
	                 "oppositeWon" => json_encode($game->oppositeUserHasWon($user)),
	                 "userWon" => json_encode($game->userHasWon($user)));
	}
}