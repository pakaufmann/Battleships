<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a single game field
 *
 * @author kufi
 *
 * @ORM\Entity
 */
class Field2 extends Field
{
	public function __construct($x, $y)
	{
		parent::__construct($x, $y);
	}	
}