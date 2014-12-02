<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg\Events;


use Tcg\Event;

class ChangeUnit extends Event {

    const ACTION_ADD_ARMOR = 'addArmor';
    const ACTION_REMOVE_ARMOR = 'removeArmor';
    const ACTION_ADD_KEYWORD = 'addKeyword';
    const ACTION_REMOVE_KEYWORD = 'removeKeyword';

    public function execute($target, $triggerData = null)
    {
        if (isset($triggerData['cardId'])) {
            $target = $triggerData['cardId'];
        } else if (isset($this->data['target'])) {
            $target = $this->data['target'];
        }
        $card = $this->game->getCard($target);

        $unit = $card->unit;
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
                $unit->addKeyword($this->data['value']['word'], isset($this->data['value']['data']) ? $this->data['value']['data'] : null);
                break;
            case self::ACTION_REMOVE_KEYWORD:
                $unit->removeKeyword($this->data['value']['word']);
                break;
        }
    }
}