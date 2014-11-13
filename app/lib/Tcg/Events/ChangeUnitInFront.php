<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg\Events;


use Tcg\Event;
use Tcg\Game;

class ChangeUnitInFront extends Event {

    const ACTION_ADD_ARMOR = 'addArmor';
    const ACTION_REMOVE_ARMOR = 'removeArmor';
    const ACTION_ADD_KEYWORD = 'addKeyword';
    const ACTION_REMOVE_KEYWORD = 'removeKeyword';

    private static $opposite = array(
        self::ACTION_ADD_ARMOR => self::ACTION_REMOVE_ARMOR,
        self::ACTION_REMOVE_ARMOR => self::ACTION_ADD_ARMOR,
        self::ACTION_REMOVE_KEYWORD => self::ACTION_ADD_KEYWORD,
        self::ACTION_ADD_KEYWORD => self::ACTION_REMOVE_KEYWORD,
    );

    private static $add = array(
        self::ACTION_ADD_KEYWORD,
        self::ACTION_ADD_ARMOR
    );

    public function execute($target, $data = null)
    {
        if (isset($data['cardId'])) {
            $target = $data['cardId'];
        }
        $card = $this->game->getCard($target);
        $cardInFront = $this->game->field->getNeibourUnit($card, 0, -1);
        if ($cardInFront) {
            $unit = $cardInFront->unit;
            switch ($this->data['action']) {
                case self::ACTION_ADD_ARMOR:
                    $unit->armor += $this->data['value'];
                    $unit->maxArmor += $this->data['value'];
                    break;
                case self::ACTION_REMOVE_ARMOR:
                    $unit->armor -= $this->data['value'];
                    $unit->maxArmor -= $this->data['value'];
                    if ($unit->armor < 0) {
                        $unit->armor = 0;
                    }
                    if ($unit->maxArmor < 0) {
                        $unit->maxArmor = 0;
                    }
                    break;
                case self::ACTION_ADD_KEYWORD:
                    $keywordData = isset($this->data['value']['data']) ? $this->data['value']['data'] : null;
                    $unit->addKeyword($this->data['value']['word'], $keywordData);
                    break;
                case self::ACTION_REMOVE_KEYWORD:
                    $unit->removeKeyword($this->data['value']['word']);
                    break;
            }
        }
        if (in_array($this->data['action'], self::$add)) {
            list ($x, $y) = $this->game->field->getRelativeCoordinats(0, -1, $card);

            $moveToCellEventId = $this->game->addEvent(
                Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL,
                $x . '_' . $y,
                '\Tcg\Events\ChangeUnit',
                ['action' => $this->data['action'], 'value' => $this->data['value']],
                null, [[Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE, $card->id], [Game::EVENT_TRIGGER_UNIT_DEATH, $card->id]]
            );

            $moveToCellEventId2 = $this->game->addEvent(
                Game::EVENT_TRIGGER_UNIT_MOVE_FROM_CELL,
                $x . '_' . $y,
                '\Tcg\Events\ChangeUnit',
                ['action' => self::$opposite[$this->data['action']], 'value' => $this->data['value']],
                null, [[Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE, $card->id], [Game::EVENT_TRIGGER_UNIT_DEATH, $card->id]]
            );
            // // when the source will live
            // $this->game->addEvent(
            //     Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE,
            //     $card->id,
            //     '\Tcg\Events\RemoveEvent',
            //     [
            //         'eventTrigger' => Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL,
            //         'eventTarget' => $x . '_' . $y,
            //         'eventId' => [$moveToCellEventId]
            //     ],
            //     1
            // );
            // $this->game->addEvent(
            //     Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE,
            //     $card->id,
            //     '\Tcg\Events\RemoveEvent',
            //     [
            //         'eventTrigger' => Game::EVENT_TRIGGER_UNIT_MOVE_FROM_CELL,
            //         'eventTarget' => $x . '_' . $y,
            //         'eventId' => [$moveToCellEventId2]
            //     ],
            //     1
            // );
            // // when the source will die
            // $this->game->addEvent(
            //     Game::EVENT_TRIGGER_UNIT_DEATH,
            //     $card->id,
            //     '\Tcg\Events\RemoveEvent',
            //     [
            //         'eventTrigger' => Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL,
            //         'eventTarget' => $x . '_' . $y,
            //         'eventId' => [$moveToCellEventId]
            //     ],
            //     1, [[Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE, $card->id]] 
            // );
            // $this->game->addEvent(
            //     Game::EVENT_TRIGGER_UNIT_DEATH,
            //     $card->id,
            //     '\Tcg\Events\RemoveEvent',
            //     [
            //         'eventTrigger' => Game::EVENT_TRIGGER_UNIT_MOVE_FROM_CELL,
            //         'eventTarget' => $x . '_' . $y,
            //         'eventId' => [$moveToCellEventId2]
            //     ],
            //     1, [Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE, $card->id]
            // );
        }
    }
}