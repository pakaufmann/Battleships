<?php
namespace kufi\BattleshipBundle\Ai;
use kufi\BattleshipBundle\Ai\AiStrategy;

/**
 * also shoots randomly, but starts shooting adjancent fields as soon as it hits a ship
 * implemented as state machine, with 4 states
 * the four states are: shootRandom, shootNeighbours, shootLine, shootOppositeLine
 * state machine setup:
 * 
 *  +---------------all neighbours hit ---+              +----------on hit-----------+
 *  v                                     |              v                           |
 *  +-no hit-+                +-no hit-+  |              +-on hit-+                  |
 *  v        |                v        |  |              v        |                  |
 * shootRandom----on hit---->shootNeighbours---on hit--->shootLine---no hit---->shootOppositeLine
 *  ^                                                                                |
 *  |                                                                                |
 *  +-----------------------------------------no hit---------------------------------+
 * 
 * @author kufi
 *
 */
class DifficultAi implements AiStrategy
{
    private $states;
    private $session;
    private $actualState;
    private $sessionVariable;

    public function __construct(\Symfony\Component\HttpFoundation\Session $session)
    {
        $this->states["shootRandom"] = new ShootRandom($session);
        $this->states["shootNeighbours"] = new ShootNeighbours($session);
        $this->states["shootLine"] = new ShootLine($session);
        $this->states["shootOppositeLine"] = new ShootOppositeLine($session);

        $this->session = $session;
        $this->actualState = 0;
    }

    public function doMove(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->sessionVariable = "difficult_ai_state_" . $game->getId();
        
        $sessionState = $this->session->get($this->sessionVariable, "shootRandom");
        
        $this->actualState = $this->states[$sessionState];
        $field = $this->actualState->doMove($game);
        
        return $field;
    }

    public function getDifficulty()
    {
        return 2;
    }

    public function getName()
    {
        return "Difficult AI";
    }

    public function hasHit()
    {
        $this->session->set($this->sessionVariable, $this->actualState->getStateOnHit());
    }

    public function hasNotHit()
    {
        $this->session->set($this->sessionVariable, $this->actualState->getStateOnNotHit());
    }

}

interface DifficultAiState
{

    public function doMove(\kufi\BattleshipBundle\Entity\Game $game);
    public function getStateOnHit();
    public function getStateOnNotHit();
}

/**
 * initial state, just starts to shoot random fields
 * on hit, changes to state "shootNeighbours"
 * 
 * @author kufi
 *
 */
class ShootRandom implements DifficultAiState
{
    private $session;
    private $gameId;
    private $shotField;

    public function __construct(\Symfony\Component\HttpFoundation\Session $session)
    {
        $this->session = $session;
    }

    public function doMove(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->gameId = $game->getId();

        $ret = $game->getUser1Fields()->filter(function ($field) {
            return !$field->getIsHit();
        });

        //return empty array if all fields have been shot
        if ($ret->count() == 0)
        {
            return array();
        }

        //shoot onto fields
        $rand = mt_rand(0, $ret->count() - 1);
        $keys = $ret->getKeys();

        $this->shotField = $ret->get($keys[$rand]);
        return $this->shotField;
    }

    public function getStateOnHit()
    {
        $this->session->set("difficult_ai_initial_hit_x" . $this->gameId, $this->shotField->getX());
        $this->session->set("difficult_ai_initial_hit_y" . $this->gameId, $this->shotField->getY());
        return "shootNeighbours";
    }

    public function getStateOnNotHit()
    {
        return "shootRandom";
    }
}

/**
 * starts to shoot all unshot neighbourfields of the hit field
 * if a field is hit, moves to state "shootLine", if no field is hit stay in this mode
 * unless there are no more fields to shoot around the last hit field
 * @author kufi
 *
 */
class ShootNeighbours implements DifficultAiState
{
    private $session;
    private $allFieldsShot;
    private $shotField;
    private $gameId;

    public function __construct(\Symfony\Component\HttpFoundation\Session $session)
    {
        $this->session = $session;
        $this->allFieldsShot = false;
    }

    public function doMove(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->gameId = $game->getId();
        $x = $this->session->get("difficult_ai_initial_hit_x" . $game->getId(), "");
        $y = $this->session->get("difficult_ai_initial_hit_y" . $game->getId(), "");

        //find all neighbouring fields which are not already hit
        $fields = $game->getUser1Fields()->filter(function ($field) use ($x, $y) {
            return ($field->getX() == $x && $field->getY() == $y - 1 || $field->getX() == $x && $field->getY() == $y + 1 || $field->getX() == $x + 1 && $field->getY() == $y || $field->getX() == $x - 1 && $field->getY() == $y) && !$field->getIsHit();
        });

        //if all neighbouring fields have been hit, start shooting randoms
        if ($fields->count() == 0)
        {
            $this->allFieldsShot = true;
            //shoot random
            $random = new ShootRandom($this->session);
            $this->shotField = $random->doMove($game);
            return $this->shotField;
        }

        //get a field out of the chosen ones 
        $rand = mt_rand(0, $fields->count() - 1);
        $keys = $fields->getKeys();

        $this->shotField = $fields->get($keys[$rand]);
        return $this->shotField;
    }

    public function getStateOnHit()
    {
        //was random shot
        if($this->allFieldsShot) {
            $this->session->set("difficult_ai_initial_hit_x" . $this->gameId, $this->shotField->getX());
            $this->session->set("difficult_ai_initial_hit_y" . $this->gameId, $this->shotField->getY());
            return "shootNeighbours";
        }
        
        $this->session->set("difficult_ai_last_hit_x" . $this->gameId, $this->shotField->getX());
        $this->session->set("difficult_ai_last_hit_y" . $this->gameId, $this->shotField->getY());
        return "shootLine";
    }

    public function getStateOnNotHit()
    {
        if ($this->allFieldsShot)
        {
            return "shootRandom";
        } else
        {
            return "shootNeighbours";
        }
    }
}

/**
 * shoots along the line of the two last hit fields
 * stays here until it doesn't hit.
 * 
 * @author kufi
 *
 */
class ShootLine implements DifficultAiState
{
    private $session;
    private $shotField;
    private $gameId;

    public function __construct(\Symfony\Component\HttpFoundation\Session $session)
    {
        $this->session = $session;
    }

    public function doMove(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->gameId = $game->getId();
        $initialX = $this->session->get("difficult_ai_initial_hit_x" . $this->gameId);
        $initialY = $this->session->get("difficult_ai_initial_hit_y" . $this->gameId);

        $lastX = $this->session->get("difficult_ai_last_hit_x" . $this->gameId);
        $lastY = $this->session->get("difficult_ai_last_hit_y" . $this->gameId);

        //shoot vertical
        if ($initialX === $lastX)
        {
            //get direction (up or down)
            $direction = ($initialY > $lastY) ? -1 : 1;
            
            //gets next field in row
            $fields = $game->getUser1Fields()->filter(function ($field) use ($lastX, $lastY, $direction) {
                return $field->getX() == $lastX && $field->getY() == $lastY + $direction && !$field->getIsHit();
            });
        } else
        {
            //shoot horizontal
            //get direction (left or right)
            $direction = ($initialX > $lastX) ? -1 : 1;
            
            //gets next field in row
            $fields = $game->getUser1Fields()->filter(function ($field) use ($lastX, $lastY, $direction)
                    {
                        return $field->getX() == $lastX + $direction && $field->getY() == $lastY && !$field->getIsHit();
                    });
        }

        //no field found (either already hit or we are at the border of the field
        if ($fields->count() == 0)
        {
            //TODO start to shoot into opposite direction
            $opposite = new ShootOppositeLine($this->session);
            $this->shotField = $opposite->doMove($game);
            return $this->shotField;
        }

        $this->shotField = $fields->first();
        return $this->shotField;
    }

    public function getStateOnHit()
    {
        $this->session->set("difficult_ai_last_hit_x" . $this->gameId, $this->shotField->getX());
        $this->session->set("difficult_ai_last_hit_y" . $this->gameId, $this->shotField->getY());
        return "shootLine";
    }

    public function getStateOnNotHit()
    {
        return "shootOppositeLine";
    }
}

/**
 * shoots into the other direction after shootLine hasn't hit anything
 * if it hits, goes back to shootLine, else will go back to shootRandom
 * @author kufi
 *
 */
class ShootOppositeLine implements DifficultAiState
{
    private $session;
    private $shotField;
    private $gameId;
    private $randomShot;
    
    public function __construct(\Symfony\Component\HttpFoundation\Session $session)
    {
        $this->session = $session;
        $this->randomShot = false;
    }    
    
    public function doMove(\kufi\BattleshipBundle\Entity\Game $game)
    {
        $this->gameId = $game->getId();
        $initialX = $this->session->get("difficult_ai_initial_hit_x" . $this->gameId);
        $initialY = $this->session->get("difficult_ai_initial_hit_y" . $this->gameId);

        $lastX = $this->session->get("difficult_ai_last_hit_x" . $this->gameId);
        $lastY = $this->session->get("difficult_ai_last_hit_y" . $this->gameId);
        
        //shoot vertical
        if ($initialX === $lastX)
        {
            //get direction (up or down)
            $direction = ($initialY > $lastY) ? 1 : -1;
            
            //get next field
            $fields = $game->getUser1Fields()->filter(function ($field) use ($initialX, $initialY, $direction) {
                return $field->getX() == $initialX && $field->getY() == $initialY + $direction && !$field->getIsHit();
            });
        } else
        {
            //shoot horizontal
            //get direction (left or right)
            $direction = ($initialX > $lastX) ? 1 : -1;
            
            //get next field
            $fields = $game->getUser1Fields()->filter(function ($field) use ($initialX, $initialY, $direction) {
                return $field->getX() == $initialX + $direction && $field->getY() == $initialY && !$field->getIsHit();
            });
        }
        
        //no field found (already hit)
        if ($fields->count() == 0)
        {
            //TODO shoot random
            $this->randomShot = true;
            
            $random = new ShootRandom($this->session);
            $this->shotField = $random->doMove($game);
            return $this->shotField;
        }
        
        $this->shotField = $fields->first();
        return $this->shotField;
    }

    public function getStateOnHit()
    {
        if($this->randomShot)
        {
            $this->session->set("difficult_ai_initial_hit_x" . $this->gameId, $this->shotField->getX());
            $this->session->set("difficult_ai_initial_hit_y" . $this->gameId, $this->shotField->getY());
            return "shootNeighbours";
        }
        
        $this->session->set("difficult_ai_last_hit_x" . $this->gameId, $this->shotField->getX());
        $this->session->set("difficult_ai_last_hit_y" . $this->gameId, $this->shotField->getY());
        return "shootLine";
    }

    public function getStateOnNotHit()
    {
        return "shootRandom";
    }

}