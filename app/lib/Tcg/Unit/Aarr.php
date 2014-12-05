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

class Aarr extends Unit {

    protected function onDeath() {
        $x = $this->x;
        $y = $this->y;

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