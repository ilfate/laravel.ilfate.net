<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2015
 */

namespace Vortex;

class Event {
	const TYPE_TREASURE = 8; //'treasure';
	const TYPE_ARROW = 1; // 'arrow';
	const TYPE_VIEW = 2; //'view';
	const TYPE_KEYS = 3; //'keys';
	const TYPE_BOMB = 4; //'bomb';
	const TYPE_KNIGHT = 5; //'knight';
	const TYPE_PORTAL = 6; //'portal';
	const TYPE_VORTEX = 7; //'vortex';

	protected $wasSetup = false;

	protected $game;

	protected $data = [
		'x' => false,
		'y' => false,
		'eventId' => false,
		'eventTypeId' => false,
		'visible' => false,
		'accessible' => false,
		'activated' => false,
	];

	protected $changes = [];

	public function __construct(Vortex $game, $eventId = null, $eventTypeId = null)
	{
		$this->game = $game;
		if ($eventId) {
			$this->data['eventId'] = $eventId;
			if ($eventTypeId) {
				$this->data['eventTypeId'] = $eventTypeId;
			}
		}

	}

	public function setup($x, $y)
	{
		$this->wasSetup = true;
		$this->data['x'] = $x;
		$this->data['y'] = $y;
		$configs = \Config::get('vortex.map');
		if (!$this->data['eventId']) {
			// we need a random event
			$this->data['eventId'] = $configs['actionChanses'][array_rand($configs['actionChanses'])];
		}
		if (!$this->data['eventTypeId'] && !empty($configs['actionsTypes'][$this->data['eventId']]['types'])) {
			// and we need a type
			$key = array_rand($configs['actionsTypes'][$this->data['eventId']]['chances']);
			$this->data['eventTypeId'] = $configs['actionsTypes'][$this->data['eventId']]['chances'][$key];
		}
	}

	public function execute()
	{
		if (!$this->wasSetup) {
			throw new Exception("Event triggered, but was never setup!", 1);
		}
		$configs = \Config::get('vortex.map');
		switch($this->data['eventId']) {
			case self::TYPE_ARROW:
				$cells = $this->getCellsByString($configs['actionsTypes'][$this->data['eventId']]['types'][$this->data['eventTypeId']]);
				foreach ($cells as $coordinats) {
					$this->game->cellView($coordinats[0], $coordinats[1]);
					$this->game->cellAccess($coordinats[0], $coordinats[1]);
					$this->game->cellActivate($coordinats[0], $coordinats[1]);
				}
				break;
			case self::TYPE_VIEW:
				$cells = $this->getCellsByString($configs['actionsTypes'][$this->data['eventId']]['types'][$this->data['eventTypeId']]);
				if (count($cells) == 1) {
					$currentCell = $cells[0];
					$range = $configs['actionsTypes'][$this->data['eventId']]['range'];
					$relation = $configs['actionsTypes'][$this->data['eventId']]['types'][$this->data['eventTypeId']];
					for ($i = 0; $i <= $range; $i++) {
						$currentCell = $this->getAbsoluteCoordinate($relation, $currentCell[0], $currentCell[1]);
						$cells[] = $currentCell;
					}
				}
				foreach ($cells as $coordinats) {
					$this->game->cellView($coordinats[0], $coordinats[1]);
				}
				break;
				case self::TYPE_KEYS:
					break;
				case self::TYPE_BOMB:
					break;
				case self::TYPE_KNIGHT:
					break;
				case self::TYPE_PORTAL:
					break;
				case self::TYPE_VORTEX:
					break;
				case self::TYPE_TREASURE:
					break;
		}
		$cells = $this->getCellsByString($configs['defaultAccessDirections']);
		foreach ($cells as $coordinats) {
			$this->game->cellAccess($coordinats[0], $coordinats[1]);
		}
	}

	public function setVisible()
	{
		$this->data['visible'] = true;
		$this->changes['visible'] = true;
		return $this;
	}
	public function isVisible()
	{
		return $this->data['visible'];
	}
	public function setAccessible()
	{
		$this->data['accessible'] = true;
		$this->changes['accessible'] = true;
		return $this;
	}
	public function isAccessible()
	{
		return $this->data['accessible'];
	}
	public function setActivated()
	{
		$this->data['activated'] = true;
		$this->changes['activated'] = true;
		return $this;
	}
	public function isActivated()
	{
		return $this->data['activated'];
	}

	public function export() {
		return $this->data;
	}

	public function import($data)
	{
		$this->data = $data;
		$this->wasSetup = true;
	}

	public function getChanges()
	{
		$return = $this->changes;
		if ($this->isVisible() || $this->isActivated()) {
			$return['eventId'] = $this->data['eventId'];
			$return['eventTypeId'] = $this->data['eventTypeId'];
		}
		return $return;
	}

	protected function getCellsByString($string)
	{
		$result = [];
		$strings = explode('+', $string);
		foreach ($strings as $relation) {
			$result[] = $this->getAbsoluteCoordinate($relation, $this->data['x'], $this->data['y']);
		}
		return $result;
	}

	protected function getAbsoluteCoordinate($relation, $x, $y)
	{
		switch ($relation) {
			case '0':
				return [$x, $y - 1];
			case '1':
				return [$x + 1, $y];
			case '2':
				return [$x, $y + 1];
			case '3':
				return [$x - 1, $y];
			case '0.5':
				return [$x + 1, $y - 1];
			case '1.5':
				return [$x + 1, $y + 1];
			case '2.5':
				return [$x - 1, $y + 1];
			case '3.5':
				return [$x - 1, $y - 1];
		}
	}
}