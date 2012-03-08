<?php
namespace kufi\BattleshipBundle\Ai;

interface AiStrategy
{
	/**
	 * starts a move an returns a field to shoot on
	 * 
	 * @return kufi\BattleshipBundle\Entity\Field
	 */
	public function doMove(\kufi\BattleshipBundle\Entity\Game $game);
	
	public function hasHit();
	
	public function hasNotHit();
	
	/**
	 * returns the difficulty level of the ai
	 * 
	 * @return int
	 */
	public function getDifficulty();
	
	/**
	 * returns a human readable name for the ai
	 */
	public function getName();
}