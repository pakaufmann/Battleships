<?php
namespace kufi\BattleshipBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * class for a registered user
 * 
 * @ORM\Entity
 * @UniqueEntity("username")
 */
class User implements UserInterface {
	
	/**
	 * 
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;
	
	/**
	 * 
	 * @ORM\Column(type="string", length="255")
	 */
	protected $username;
	
	/**
	 * 
	 * @ORM\Column(type="string", length="255")
	 */
	protected $password;
	
	/**
	 * 
	 * @ORM\Column(type="string", length="255")
	 */
	protected $firstName;
	
	/**
	 *
	 * @ORM\Column(type="string", length="255")
	 */
	protected $lastName;
	
	/**
	 * 
	 * @ORM\Column(type="string", length="255")
	 */
	protected $salt;
	
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $gamesWon;
	
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $gamesLost;
	
	/**
	 * 
	 * @ORM\Column(type="integer")
	 */
	protected $gamesPlayed;
	
	public function __construct()
	{
		$this->salt = base_convert(sha1(uniqid(mt_rand(), true)), 16, 36);
		$this->gamesWon = 0;
		$this->gamesLost = 0;
		$this->gamesPlayed = 0;
	}
	
	public function getRoles() {
		return array("ROLE_USER");
	}
	public function getPassword() {
		return $this->password;
	}
	public function getSalt() {
		return $this->salt;
	}
	public function getUsername() {
		return $this->username;
	}
	public function eraseCredentials() {
	}
	public function equals(UserInterface $user) {
		return $this->username == $user->getUsername();
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
     * Set username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Set password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set firstName
     *
     * @param string $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * Get firstName
     *
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * Get lastName
     *
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * Set salt
     *
     * @param string $salt
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;
    }

    /**
     * Set gamesWon
     *
     * @param integer $gamesWon
     */
    public function setGamesWon($gamesWon)
    {
        $this->gamesWon = $gamesWon;
    }

    /**
     * Get gamesWon
     *
     * @return integer 
     */
    public function getGamesWon()
    {
        return $this->gamesWon;
    }

    /**
     * Set gamesLost
     *
     * @param integer $gamesLost
     */
    public function setGamesLost($gamesLost)
    {
        $this->gamesLost = $gamesLost;
    }

    /**
     * Get gamesLost
     *
     * @return integer 
     */
    public function getGamesLost()
    {
        return $this->gamesLost;
    }

    /**
     * Set gamesPlayed
     *
     * @param integer $gamesPlayed
     */
    public function setGamesPlayed($gamesPlayed)
    {
        $this->gamesPlayed = $gamesPlayed;
    }

    /**
     * Get gamesPlayed
     *
     * @return integer 
     */
    public function getGamesPlayed()
    {
        return $this->gamesPlayed;
    }
    
    public function addPlayedGame()
    {
    	$this->gamesPlayed++;
    }
    
    public function addWonGame()
    {
    	$this->gamesWon++;
    }
    
    public function addLostGame()
    {
    	$this->gamesLost++;
    }
}