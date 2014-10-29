<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Unit {

	public $totalHealth;
	public $currentHealth;


	public static function createFromConfig($config)
	{
		$unit = new Unit();
		$unit->totalHealth = $config['totalHealth'];

		return $unit;
	}

	public function import($data)
	{
		$this->currentHealth = $date['currentHealth'];
		return $this;
	}
}