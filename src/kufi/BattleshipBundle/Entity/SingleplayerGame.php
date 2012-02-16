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
    	parent::__construct();
    	
    	$this->difficulty = $difficulty;
    	
    	//create the fields
    	for($x = 0;$x<$fieldSize;$x++) {
    		for($y = 0;$y<$fieldSize;$y++) {
    			$this->addUser1Field(new Field1($x, $y));
    			$this->addUser2Field(new Field2($x, $y));
    		}
    	}
    	
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