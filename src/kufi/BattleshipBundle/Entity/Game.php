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
	 * @ORM\OneToMany(targetEntity="Field", mappedBy="game")
	 */
	protected $userFields;
	
	/**
	 * 
	 * @ORM\OneToMany(targetEntity="Field", mappedBy="game")
	 */
	protected $aiFields;
	
    public function __construct()
    {
        $this->userFields = new \Doctrine\Common\Collections\ArrayCollection();
    	$this->aiFields = new \Doctrine\Common\Collections\ArrayCollection();
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
    public function addUserField(\kufi\BattleshipBundle\Entity\Field $userField)
    {
        $this->userFields[] = $userField;
    }
    
    /**
     * Add aiFields
     *
     * @param kufi\BattleshipBundle\Entity\Field $userFields
     */
    public function addAiField(\kufi\BattleshipBundle\Entity\Field $aiField)
    {
    	$this->userFields[] = $aiField;
    }
    
    /**
     * Get userFields
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getUserFields()
    {
        return $this->userFields;
    }

    /**
     * Get aiFields
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getAiFields()
    {
        return $this->aiFields;
    }
}