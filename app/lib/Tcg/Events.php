<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg;


trait Events {


    public $events = [];

    public function triggerEvent($eventTrigger, $target, array $data = null)
    {
        if (!empty($this->events[$eventTrigger][$target])) {
            foreach ($this->events[$eventTrigger][$target] as $key => &$eventData) {
                if ($eventData['times'] === null || $eventData['times'] > 0) {
                    $event = new $eventData['obj']($eventData['data'], $this);
                    $event->execute($target, $data, $eventData['times']);
                    if ($eventData['times'] !== null) {
                        $eventData['times']--;
                    }
                } else {
                    unset ($this->events[$eventTrigger][$target][$key]);
                }
            }
        }

    }

    public function addEvent($eventTrigger, $eventTarget, $event, $data, $times = null)
    {
        if (!isset($this->events[$eventTrigger])) {
            $this->events[$eventTrigger] = [];
        }
        if (!isset($this->events[$eventTrigger][$eventTarget])) {
            $this->events[$eventTrigger][$eventTarget] = [];
        }
        $lastEvent = end($this->events[$eventTrigger][$eventTarget]);
        if ($lastEvent === false) {
            $eventId = 0;
        } else {
            $eventId = $lastEvent['id'] + 1;
        }
        $this->events[$eventTrigger][$eventTarget][$eventId] = ['obj' => $event, 'data' => $data, 'times' => $times, 'id' => $eventId];
        return $eventId;
    }

    public function removeEvent($eventTrigger, $eventTarget, $eventId) {
        if (isset($this->events[$eventTrigger][$eventTarget][$eventId])) {
            unset($this->events[$eventTrigger][$eventTarget][$eventId]);
        }
    }
} 