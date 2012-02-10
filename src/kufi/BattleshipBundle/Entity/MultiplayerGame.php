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

    public function __construct()
    {
        parent::__construct();
    }
}