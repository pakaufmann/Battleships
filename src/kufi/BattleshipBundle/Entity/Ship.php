<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a single ship
 * 
 * @author kufi
 * 
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="ship_type", type="string")
 * @ORM\DiscriminatorMap({"s1" = "Ship1", "s2" = "Ship2"})
 */
abstract class Ship
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
	 * @ORM\Column(type="integer")
	 */
	protected $startX;
	
	/**
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $startY;
	
	/**
	 *
	 * @ORM\Column(type="integer")
	 */
	protected $length;
	
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $orientation;
	
	public function __construct($startX, $startY, $length, $orientation)
	{
		$this->startX = $startX;
		$this->startY = $startY;
		$this->length = $length;
		$this->orientation = $orientation;
	}
	
	public function setGame(\kufi\BattleshipBundle\Entity\Game $game)
	{
		$this->game = $game;
	}
	
	public function getLength()
	{
		return $this->length;
	}
	
	public function getOrientation()
	{
		return $this->orientation;
	}
	
	public function getX()
	{
		return $this->startX;
	}
	
	public function getY()
	{
		return $this->startY;
	}
}