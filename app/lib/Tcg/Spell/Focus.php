<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Spell;

use ClassPreloader\Config;
use Tcg\Spell;
use Tcg\Card;
use \Tcg\Unit;

class Focus extends Spell {

	public function castUnit(Card $target)
	{
		
		$target->unit->addKeyword(Unit::KEYWORD_FOCUS);

		//$this->card->game->log->logText(__CLASS__ . " spell was cast on " . $target->unit->name );
	}


}