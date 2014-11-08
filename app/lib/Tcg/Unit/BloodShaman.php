<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use Tcg\Events\ChangeUnit;
use Tcg\Events\ChangeUnitInFront;
use Tcg\Game;
use \Tcg\Unit;
use \Tcg\Card;

class BloodShaman extends Unit {

    public function deploy()
    {
        parent::deploy();
//        $cardInFront = $this->card->game->field->getNeibourUnit($this->card, 0, -1);
//        $team = $this->card->game->teams[$this->card->owner];
//        if ($cardInFront && in_array($cardInFront->owner, $team)) {
//            $cardInFront->unit->armor += 3;
//            $cardInFront->unit->maxArmor += 3;
//        }
        $keyword = Unit::KEYWORD_SHIELD;
        $value = 3;
        $addKeywordData = ['action' => ChangeUnitInFront::ACTION_ADD_KEYWORD, 'value' => ['word' => $keyword, 'data' => $value]];
        $event = new ChangeUnitInFront($addKeywordData, $this->card->game);
        $event->execute($this->card->id);

        $this->card->game->addEvent(
            Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE,
            $this->card->id,
            '\Tcg\Events\ChangeUnitInFront',
            ['action' => ChangeUnitInFront::ACTION_REMOVE_KEYWORD, 'value' => ['word' => $keyword]]
        );
        $this->card->game->addEvent(
            Game::EVENT_TRIGGER_AFTER_UNIT_MOVE,
            $this->card->id,
            '\Tcg\Events\ChangeUnitInFront',
            $addKeywordData
        );

        // if we will deploy some one in front
        list ($x, $y) = $this->card->game->field->getRelativeCoordinats(0, -1, $this->card);
        $deployEventId = $this->card->game->addEvent(
            Game::EVENT_TRIGGER_UNIT_DEPLOY_TO_CELL,
            $x . '_' . $y,
            '\Tcg\Events\ChangeUnit',
            $addKeywordData
        );

        $this->card->game->addEvent(
            Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE,
            $this->card->id,
            '\Tcg\Events\RemoveEvent',
            [
                'eventTrigger' => Game::EVENT_TRIGGER_UNIT_DEPLOY_TO_CELL,
                'eventTarget' => $x . '_' . $y,
                'eventId' => [$deployEventId]
            ],
            1
        );
    }
}