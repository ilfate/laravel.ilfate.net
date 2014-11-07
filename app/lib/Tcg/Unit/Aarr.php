<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use \Tcg\Unit;
use \Tcg\Card;

class Aarr extends Unit {

	protected function getTargets()
    {
    	$targets = [];
        $possibleTargets = [
            $this->card->game->field->getNeibourUnit($this->card, -1, -1),
            $this->card->game->field->getNeibourUnit($this->card, -1, 1),
            $this->card->game->field->getNeibourUnit($this->card, 1, 1),
            $this->card->game->field->getNeibourUnit($this->card, 1, -1),
        ];
        $team = $this->card->game->teams[$this->card->owner];
        foreach ($possibleTargets as $target) {
            if ($target && !in_array($target->owner, $team)) {
                $targets[] = $target;
            } 
        }
    	
    	return $targets;
    }
}