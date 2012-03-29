<?php
namespace kufi\BattleshipBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * represents a multiplayer game
 * @author kufi
 *
 * @ORM\Entity
 */
class MultiplayerGame extends Game
{
    
    /**
     * @ORM\Column(type="string")
     */
    private $name;
    
    public function getName()
    {
        return $this->name;
    }
    
    public function setName($name)
    {
        $this->name = $name;
    }
    
    /**
     * 
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $playerOne;
    
    public function getPlayerOne()
    {
        return $this->playerOne;
    }
    
    public function setPlayerOne($playerOne)
    {
        $this->playerOne = $playerOne;
    }
    
    /**
     *
     * @ORM\ManyToOne(targetEntity="User")
     */
    private $playerTwo;
    
    public function getPlayerTwo()
    {
        return $this->playerTwo;
    }
    
    public function setPlayerTwo($playerTwo)
    {
        $this->playerTwo = $playerTwo;
    }
    
    /**
     * 
     * @ORM\Column(type="boolean")
     */
    public $user1Ready;
    public function isUser1Ready()
    {
        return $this->user1Ready;
    }
    
    public function setUser1Ready()
    {
        $this->user1Ready = true;
    }
    
    /**
     *
     * @ORM\Column(type="boolean")
     */
    public $user2Ready;
    public function isUser2Ready()
    {
        return $this->user2Ready;
    }
    
    public function setUser2Ready()
    {
        $this->user2Ready = true;
    }
    
    /**
     * 
     @ORM\ManyToOne(targetEntity="User")
     */
    public $userTurn;
    public function getUserTurn()
    {
        return $this->userTurn;
    }
    
    public function setUserTurn($user)
    {
        $this->userTurn = $user;
    }
    
    public function __construct()
    {
        $this->user1Ready = false;
        $this->user2Ready = false;
        parent::__construct(10);
    }
    
    public function getShipsForUser(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->getUser1Ships();
        }
        return $this->getUser2Ships();
    }
    
    public function addShipForUser(\kufi\BattleshipBundle\Entity\User $user, $x, $y, $length, $orientation)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            $ship = new Ship1($x, $y, $length, $orientation);
            return $this->addUser1Ship($ship);
        }
        else
        {
            $ship = new Ship2($x, $y, $length, $orientation);
            return $this->addUser2Ship($ship);
        }
    }
    
    public function setUserReady(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            $this->setUser1Ready();
        }
        else
        {
            $this->setUser2Ready();
        }
    }
    
    public function hasShipWithLength($length, \kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId()) 
        {
            return $this->hasShip1WithLength($length);
        }
        return $this->hasShip2WithLength($length);
    }
    
    public function isOppositeUserReady(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->isUser2Ready();
        }
        return $this->isUser1Ready();
    }
    
    public function checkOppositeUserAlreadyHit($x, $y, \kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->checkAlreadyHitUser2($x, $y);
        }
        return $this->checkAlreadyHitUser1($x, $y);
    }
    
    public function hitFieldOppositeUser($x, $y, \kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->hitFieldUser2($x, $y);
        }
        return $this->hitFieldUser1($x, $y);
    }
    
    public function userHasWon(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->user1HasWon();
        }
        return $this->user2HasWon();
    }
    
    public function oppositeUserHasWon(\kufi\BattleshipBundle\Entity\User $user)
    {
        return $this->userHasWon($this->getOppositeUser($user));
    }
    
    public function getOppositeUser(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->playerTwo;
        }
        
        return $this->playerOne;
    }
    
    public function isUserTurn(\kufi\BattleshipBundle\Entity\User $user)
    {
        if(!$this->user1Ready || !$this->user2Ready)
        {
            return false;
        }
        return $this->userTurn->getId() == $user->getId();
    }
    
    public function getUserFields(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->getUser1Fields();
        }
        
        return $this->getUser2Fields();
    }
    
    public function getOppositeFields(\kufi\BattleshipBundle\Entity\User $user)
    {
        if($this->playerOne->getId() == $user->getId())
        {
            return $this->getUser2Fields();
        }
        
        return $this->getUser1Fields();
    }
    
    public function getShotFields(\kufi\BattleshipBundle\Entity\User $user)
    {
        $fields = $this->user2Fields;
        if($this->playerOne->getId() == $user->getId())
        {
            $fields = $this->user1Fields;
        }
        
        return $fields->filter(function($field) {
            return $field->getIsHit();
        });
    }
}