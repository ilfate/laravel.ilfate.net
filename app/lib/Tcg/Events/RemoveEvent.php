<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg\Events;


use Tcg\Event;

class RemoveEvent extends Event {

    public function execute($target, $data = null)
    {
        $eventTrigger = $this->data['eventTrigger'];
        $eventTarget = $this->data['eventTarget'];
        $eventIds = $this->data['eventId'];
        if(!is_array($eventIds)) {
            $eventIds = [$eventIds];
        }
        foreach ($eventIds as $eventId) {
            $this->game->removeEvent($eventTrigger, $eventTarget, $eventId);
        }
    }
}