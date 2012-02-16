<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a single game field
 * 
 * @author kufi
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="field_type", type="string")
 * @ORM\DiscriminatorMap({"f1" = "Field1", "f2" = "Field2"})
 */
abstract class Field
{
	/**
	 * 
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * 
	 * @ORM\ManyToOne(targetEntity="Game", inversedBy="id")
	 */
	protected $game;
	
	/**
	 * 
	 * @ORM\Column(type="boolean")
	 */
	protected $hasShip;
	
	/**
	 * 
	 * @ORM\Column(type="boolean")
	 */
	protected $isHit;
	
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $xCoord;
	
	/**
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $yCoord;
	
	public function __construct($x, $y)
	{
		$this->xCoord = $x;
		$this->yCoord = $y;
		$this->hasShip = false;
		$this->isHit = false;
	}
	
    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set hasShip
     *
     * @param boolean $hasShip
     */
    public function setHasShip($hasShip)
    {
        $this->hasShip = $hasShip;
    }

    /**
     * Get hasShip
     *
     * @return boolean 
     */
    public function getHasShip()
    {
        return $this->hasShip;
    }

    /**
     * Set isHit
     *
     * @param boolean $isHit
     */
    public function setIsHit($isHit)
    {
        $this->isHit = $isHit;
    }

    /**
     * Get isHit
     *
     * @return boolean 
     */
    public function getIsHit()
    {
        return $this->isHit;
    }

    /**
     * Set game
     *
     * @param kufi\BattleshipBundle\Entity\Game $game
     */
    public function setGame(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->game = $game;
    }

    /**
     * Get game
     *
     * @return kufi\BattleshipBundle\Entity\Game 
     */
    public function getGame()
    {
        return $this->game;
    }
    
    /**
     * Get X Coordinate
     * 
     * @return Integer
     */
    public function getX()
    {
    	return $this->xCoord;
    }
    
    /**
     * Set X Coordinate
     * 
     */
    public function setX($x)
    {
    	$this->xCoord = $x;
    }
    
    /**
     * Get Y Coordinate
     * @return Integer
     */
    public function getY()
    {
    	return $this->yCoord;
    }
    
    /**
     * Set Y Coordinate
     * 
     * @param Integer $y
     */
    public function setY($y)
    {
    	$this->yCoord = $y;
    }
}