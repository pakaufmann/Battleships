<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a single ship for player 1
 *
 * @author kufi
 *
 * @ORM\Entity
 */
class Ship1 extends Ship
{
	public function __construct($startX, $startY, $length, $orientation)
	{
		parent::__construct($startX, $startY, $length, $orientation);
	}
}