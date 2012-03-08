<?php
namespace kufi\BattleshipBundle\Ai;

class AiFactory
{
	private $aiStrategies;
	
	/**
	 * start the factory with the given strategies
	 * 
	 * @param aiStrategies array
	 */
	public function __construct($aiStrategies)
	{
		$this->aiStrategies = $aiStrategies;
	}
	
	public function getStrategy($difficulty)
	{
		foreach($this->aiStrategies as $strategy)
		{
			if($strategy->getDifficulty() == $difficulty) {
				return $strategy;
			}
		}
		
		return null;
	}
	
	public function getAis()
	{
		$ais = array();
		foreach($this->aiStrategies as $ai)
		{
			$ais[$ai->getDifficulty()] = $ai->getName();
		}
		
		return $ais;
	}
}