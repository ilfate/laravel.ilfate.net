<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use Tcg\FieldObject;
use Tcg\Game;
use \Tcg\Unit;
use \Tcg\Card;

class UnitCanThrowAxe extends Unit {

    protected function afterAttack($damage, Card $target) {
        if (!empty($this->data['axe'])) {
            $this->attackRange = 1;
            $this->setAttack([1, 3]);
            unset($this->data['axe']);

            $x = $target->unit->x;
            $y = $target->unit->y;

            $fieldObject = FieldObject::createFromConfig(2, $this->card->game->field);
            $fieldObject->x = $x;
            $fieldObject->y = $y;
            $this->card->game->field->addObject($fieldObject);

            $this->card->game->addEvent(
                Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL,
                $x . '_' . $y,
                '\Tcg\Events\GetAxe',
                ['mapObjectId' => $fieldObject->id]
            );
            
        }
    }
}