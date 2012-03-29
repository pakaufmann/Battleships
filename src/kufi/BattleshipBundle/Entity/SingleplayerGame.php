<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a game in singleplayer
 * @author kufi
 * 
 * @ORM\Entity
 *
 */
class SingleplayerGame extends Game
{
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $difficulty;
    
    public function __construct($difficulty, $fieldSize)
    {
    	parent::__construct($fieldSize);
    	
    	$this->difficulty = $difficulty;
    }
    
    /**
     * Set difficulty
     *
     * @param integer $difficulty
     */
    public function setDifficulty($difficulty)
    {
        $this->difficulty = $difficulty;
    }

    /**
     * Get difficulty
     *
     * @return integer 
     */
    public function getDifficulty()
    {
        return $this->difficulty;
    }
    
    /**
     * shoots automatically for the user (if in singleplayer for the ai)
     */
    public function user2ShootAutomatically(\kufi\BattleshipBundle\Ai\AiFactory $aiFactory)
    {
    	//gets the appropriate strategy for this difficulty
    	$aiStrategy = $aiFactory->getStrategy($this->difficulty);
    	
    	$fields = array();
    	$field = $aiStrategy->doMove($this);
    	 
    	if($this->hitFieldUser1($field->getX(), $field->getY()))
    	{
    		//if we hit something, call us again and add the field to the resulting array
    		$aiStrategy->hasHit();
    		$fields = $this->user2ShootAutomatically($aiFactory);
    		$fields[] = $field;
    		return $fields;
    	}
    	else
    	{
    		$aiStrategy->hasNotHit();
    		//if we hit nothing, just return a new array with the shot field
    		return array($field);
    	}
    }
}