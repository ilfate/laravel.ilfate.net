<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use Tcg\Game;
use \Tcg\Unit;
use \Tcg\Card;

class Runner extends Unit {

    protected function afterAttack($damage, Card $target) {
        if (!empty($this->data['axe'])) {
            $this->attackRange = 1;
            $this->setAttack([1, 3]);
            unset($this->data['axe']);

            $x = $target->unit->x;
            $y = $target->unit->y;

            $this->card->game->addEvent(
                Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL,
                $x . '_' . $y,
                '\Tcg\Events\GetAxe'
            );
        }
    }
}