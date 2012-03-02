<?php
namespace kufi\BattleshipBundle\Model;

use kufi\BattleshipBundle\Entity\User;
use Doctrine\ORM\EntityManager;

class UserRepository
{
	private $em;
	
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}
	
	public function addUser(User $user)
	{
		$this->em->persist($user);
		$this->em->flush();
	}
	
	public function updateUser(User $user)
	{
		$this->em->persist($user);
		$this->em->flush();
	}
}