<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use \Tcg\Unit;

class GudTheSmith extends Unit {


	protected function attackNoTargets() {
		$team = $this->card->game->teams[$this->card->owner];
		$healTargets = $this->card->game->field->getAllPlayersUnitsInRange($this->x, $this->y, 1, $team);
		if (!$healTargets) {
			return;
		}
		$healTargets = array_filter($healTargets, function($card) {
			return $card->unit->maxArmor > $card->unit->armor;
		});
		if (!$healTargets) {
			return;
		}
		$unit = $healTargets[array_rand($healTargets)]->unit;
		$unit->armor += 3;
		if ($unit->armor > $unit->maxArmor) {
			$unit->armor = $unit->maxArmor;
		}

		$this->card->game->log->logText(__CLASS__ . " repared armor for " . $unit->name );
    }
}