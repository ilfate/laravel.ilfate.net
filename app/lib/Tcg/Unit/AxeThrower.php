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

class AxeThrower extends Unit {

	public function deploy()
    {
    	parent::deploy();
    	$damage = 5;
    	for ($i = 1; $i <= 3; $i++) {
    		$cardInFront = $this->card->game->field->getNeibourUnit($this->card, 0, -$i);
    		if ($cardInFront) {
    			break;
    		}
    		$damage--;
    	}
    	if ($cardInFront) {
    		$cardInFront->unit->applyDamage($damage, $this->card);
    		//$this->card->game->log->logAttack($this->name, $this->card->owner, $cardInFront->unit->name, $damage);
    	}
    }
}