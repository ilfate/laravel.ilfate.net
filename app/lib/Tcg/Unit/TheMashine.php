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

class TheMashine extends Unit {

	protected function getTargets()
    {
        if ($this->stepsMade > 0) {
            return [];
        }
        for ($i = 1; $i <= 3; $i ++) {
    	    $cardInFront = $this->card->game->field->getNeibourUnit($this->card, 0, -$i);
            $team = $this->card->game->teams[$this->card->owner];
            if ($cardInFront && !in_array($cardInFront->owner, $team)) {
                return [$cardInFront];
            }
        }
    	return [];
    }
}