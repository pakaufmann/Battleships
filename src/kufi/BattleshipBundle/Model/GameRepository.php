<?php
namespace kufi\BattleshipBundle\Model;

use Doctrine\ORM\EntityManager;

class GameRepository {
	
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function addGame($game)
	{
		$this->em->persist($game);
		$this->em->flush();
	}
}
