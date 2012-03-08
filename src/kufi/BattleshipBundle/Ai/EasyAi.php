<?php
namespace kufi\BattleshipBundle\Ai;
class EasyAi implements AiStrategy {
	public function doMove(\kufi\BattleshipBundle\Entity\Game $game) {
		//just randomly shoot somewhere where we havent already shot (not really sophisticated)
		//shoot until we hit an empty field
		$ret = $game->getUser1Fields()
				->filter(function ($field) {
					return !$field->getIsHit();
				});

		//return empty array if all fields have been shot
		if ($ret->count() == 0) {
			return array();
		}

		//shoot onto fields
		$rand = mt_rand(0, $ret->count() - 1);
		$keys = $ret->getKeys();

		return $ret->get($keys[$rand]);
	}

	public function getDifficulty() {
		return 1;
	}

	public function getName() {
		return "Easy AI";
	}
	
	public function hasHit() {
		//dont do anything, because we're stupid
	}
	
	public function hasNotHit() {
		//dont do anything, because we're stupid
	}

}
