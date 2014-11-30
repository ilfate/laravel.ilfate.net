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

class Armor extends Spell {

    public function castUnit(Card $target)
    {
        if (isset($this->config['data']['mode']) && $this->config['data']['mode'] == 'add') {
            $target->unit->armor += $this->config['data']['value'];
            $target->unit->maxArmor = $target->unit->armor;
            $target->unit->card->game->log->logUnitChangeArmor($target->id, $target->unit->armor, $this->config['data']['value']);
        } else {
            if ($target->unit->armor >= $target->unit->maxArmor) {
                throw new Exception("This unit have full armor", 1);
            }
            $target->unit->changeArmor($this->config['data']['value']);
        }

        $this->logCast();
    }


}