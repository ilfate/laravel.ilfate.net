<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg\Events;


use Tcg\Event;

class GetAxe extends Event {

    const ACTION_ADD_ARMOR = 'addArmor';
    const ACTION_REMOVE_ARMOR = 'removeArmor';
    const ACTION_ADD_KEYWORD = 'addKeyword';
    const ACTION_REMOVE_KEYWORD = 'removeKeyword';

    public function execute($target, $data = null)
    {
        // $target for this event is $x_$y
        $target = $data['cardId'];
        $card = $this->game->getCard($target);

        if (empty($card->unit->data['axe']) && ($card->unit instanceof \Tcg\Unit\UnitCanThrowAxe)) {
            $card->unit->setAttack([6, 6]);
            $card->unit->data['axe'] = true;
            $card->unit->attackRange = 2;
            $this->game->removeEvent($this->eventTrigger, $this->eventTarget, $this->eventId);

            $this->game->field->removeObject($this->data['mapObjectId']);
        }
    }
}