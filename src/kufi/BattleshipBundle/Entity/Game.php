<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * abstract base class for games
 * 
 * @author kufi
 *
 * @ORM\Entity
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="game_type", type="string")
 * @ORM\DiscriminatorMap({"sp" = "SingleplayerGame", "mp" = "MultiplayerGame"})
 */
abstract class Game
{
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\generatedValue
	 */
	protected $id;
	
	/**
	 *
	 * @ORM\OneToMany(targetEntity="Field1", mappedBy="game", cascade={"all"})
	 */
	protected $user1Fields;
	
	/**
	 * 
	 * @ORM\OneToMany(targetEntity="Field2", mappedBy="game", cascade={"all"})
	 */
	protected $user2Fields;
	
    public function __construct()
    {
        $this->user1Fields = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->user2Fields = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Add userFields
     *
     * @param kufi\BattleshipBundle\Entity\Field $userFields
     */
    public function addUser1Field(\kufi\BattleshipBundle\Entity\Field $userField)
    {
    	$userField->setGame($this);
        $this->user1Fields->add($userField);
    }
    
    /**
     * Add aiFields
     *
     * @param kufi\BattleshipBundle\Entity\Field $userFields
     */
    public function addUser2Field(\kufi\BattleshipBundle\Entity\Field $userField)
    {
    	$userField->setGame($this);
    	$this->user2Fields->add($userField);
    }
    
    /**
     * Get userFields
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUser1Fields()
    {
        return $this->user1Fields;
    }

    /**
     * Get aiFields
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUser2Fields()
    {
        return $this->user2Fields;
    }
}