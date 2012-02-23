<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a single ship for player 2
 *
 * @author kufi
 *
 * @ORM\Entity
 */
class Ship2 extends Ship
{
	public function __construct($startX, $startY, $length, $orientation)
	{
		parent::__construct($startX, $startY, $length, $orientation);
	}
}