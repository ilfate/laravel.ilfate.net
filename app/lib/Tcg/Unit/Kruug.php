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

class Kruug extends Unit {

	protected function afterAttack($damage, Card $target) {
    	$this->healDamage(2, $this->card);    
    }
}