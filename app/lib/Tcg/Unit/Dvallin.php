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

class Dvallin extends Unit {

	protected function afterAttack($damage, Card $target) {
		if ($this->maxArmor == $this->armor) {
			$this->maxArmor += $damage;
		} else if($damage > $this->maxArmor - $this->armor) {
			$this->maxArmor += $damage - $this->maxArmor - $this->armor;
		}
		$this->changeArmor($damage);
    }
}