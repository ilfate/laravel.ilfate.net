<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2015
 */

namespace Vortex;

class Vortex {

	const MAP_SIZE = 7;
	const AMOUNT_OF_TREASURES = 3;

	protected $map;
	protected $userCurrentClickX;
	protected $userCurrentClickY;

	public function __construct() 
	{
		
	}

	public function init($x, $y)
	{
		$this->userCurrentClickX = $x;
		$this->userCurrentClickY = $y;
	}

	public static function createFromArray($data)
	{
		$vortex = new Vortex();
		foreach ($data as $x => $row) {
			foreach ($row as $y => $cellData) {
				$vortex->map[$x][$y] = new Event($vortex);
				$vortex->map[$x][$y]->import($cellData);
			}
		}
		return $vortex;
	}

	public function export()
	{
		$data = [];
		foreach ($this->map as $x => $row) {
			foreach ($row as $y => $cell) {
				if ($cell) {
					$data[$x][$y] = $cell->export();
				}
			}
		}
		return $data;
	}

	public function getChangesToRender()
	{
		$data = [];
		foreach ($this->map as $x => $row) {
			foreach ($row as $y => $cell) {
				if ($cell) {
					$data[$x][$y] = $cell->getChanges();
				}
			}
		}
		return $data;
	}

	public function createGame()
	{
		$freeCells = array_fill(0, self::MAP_SIZE, array_fill(0, self::MAP_SIZE, ''));
		$this->map = $freeCells;
		for($i = 0; $i < self::AMOUNT_OF_TREASURES; $i++) {
			$y = array_rand($freeCells);
			$x = array_rand($freeCells[$y]);
			if (!$this->isAwayFrom($x, $y, $this->userCurrentClickX, $this->userCurrentClickY)) {
				$i--;
				continue;
			}
			unset($freeCells[$x][$y]);
			$this->map[$x][$y] = new Event($this, Event::TYPE_TREASURE);
		}
		unset($freeCells[$this->userCurrentClickX][$this->userCurrentClickY]);

		$newEvent = $this->createNewEvent($this->userCurrentClickX, $this->userCurrentClickY, Event::TYPE_VIEW, 9);
		$newEvent->setVisible();
		$newEvent->setAccessible();
		$this->cellActivate($this->userCurrentClickX, $this->userCurrentClickY);

	}

	public function cellView($x, $y)
	{
		if ($x < 0 || $y < 0 || $x >= self::MAP_SIZE || $y >= self::MAP_SIZE) {
			return false;				
		}
		if (empty($this->map[$x][$y])) {
			$this->createNewEvent($x, $y);
		}
		if (!$this->map[$x][$y]->isVisible() && !$this->map[$x][$y]->isActivated()) {
			$this->map[$x][$y]->setVisible();
		}
	}

	public function cellAccess($x, $y)
	{
		if ($x < 0 || $y < 0 || $x >= self::MAP_SIZE || $y >= self::MAP_SIZE) {
			return false;				
		}
		if (empty($this->map[$x][$y])) {
			$this->createNewEvent($x, $y);
		}
		if (!$this->map[$x][$y]->isAccessible() && !$this->map[$x][$y]->isActivated()) {
			$this->map[$x][$y]->setAccessible();
		}
	}

	public function cellActivate($x, $y)
	{
		if ($x < 0 || $y < 0 || $x >= self::MAP_SIZE || $y >= self::MAP_SIZE) {
			return false;				
		}
		if (empty($this->map[$x][$y]) || !$this->map[$x][$y]->isAccessible()) {
			return false;
		}
		if (!$this->map[$x][$y]->isActivated()) {
			$this->map[$x][$y]->setActivated();
			$this->map[$x][$y]->execute();
		}	
	}

	public function createNewEvent($x, $y, $eventId = null, $eventTypeId = null)
	{
		if (empty($this->map[$x])) {
			$this->map[$x] = [];
		}
		$this->map[$x][$y] = new Event($this, $eventId, $eventTypeId);
		$this->map[$x][$y]->setup($x, $y);
		return $this->map[$x][$y];
	}

	protected function setData($data)
	{
		$this->map = $data['map'];
	}

	protected function getCenter()
	{
		return floor(self::MAP_SIZE / 2);
	}

	protected function isAwayFrom($x, $y, $pointX, $pointY, $radius = 1)
	{
		if ($x >= $pointX - $radius && $x <= $pointX + $radius 
			&& $y >= $pointY - $radius && $y <= $pointY + $radius) {
			return false;
		}
		return true;
	}

	
}