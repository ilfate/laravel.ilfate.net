<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Spell;

use ClassPreloader\Config;
use Tcg\Events\ChangeUnit;
use Tcg\Exception;
use Tcg\Game;
use Tcg\Spell;
use Tcg\Card;
use \Tcg\Unit;

class Heal extends Spell {

    public function castUnit(Card $target)
    {
        if ($target->unit->currentHealth >= $target->unit->maxHealth) {
            throw new Exception("This unit have full health", 1);
        }
        $target->unit->healDamage($this->config['data']['value'], $this->card);

        $this->logCast();
    }


}