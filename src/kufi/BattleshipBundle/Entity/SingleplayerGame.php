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
    
    public function __construct($difficulty)
    {
    	$this->difficulty = $difficulty;
    	parent::__construct();
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
}