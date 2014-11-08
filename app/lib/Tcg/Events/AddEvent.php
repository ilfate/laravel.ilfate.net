<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg\Events;


use Tcg\Event;

class AddEvent extends Event {

    public function execute($target, $data = null)
    {
        $eventTrigger = $this->data['eventTrigger'];
        $eventTarget = $this->data['eventTarget'];
        $event = $this->data['event'];
        $data = $this->data['data'];
        $times = isset($this->data['times']) ? $this->data['times'] : null;

        $moveToCellEventId = $this->card->game->addEvent(
            $eventTrigger,
            $eventTarget,
            $event,
            $data,
            $times
        );
    }
}