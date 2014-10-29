<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

use ClassPreloader\Config;

class Unit {

	public $totalHealth;
	public $currentHealth;

    public $effects = array();


    /**
     * @var Effect\Effect[]
     */
    protected $effectObjects = array();

	public static function createFromConfig($config)
	{
		$unit = new Unit();
		$unit->totalHealth = $config['totalHealth'];

		return $unit;
	}

	public static function import($data, $unitId)
	{
        $unit = Unit::createFromConfig(\Config::get('tcg.units.' . $unitId));
		$unit->currentHealth = $data['currentHealth'];
		$unit->effects       = $data['effects'];
        $unit->initEffects();
		return $unit;
	}

    public function export()
    {
        $this->updateEffects();
        $data = [
            'currentHealth' => $this->currentHealth,
            'effects'       => $this->effects,
        ];
        return $data;
    }

    protected function initEffects()
    {
        foreach ($this->effects as $effect)
        {
            $this->effectObjects[] = new $effect[0]($effect[1]);
        }
    }

    protected function updateEffects()
    {
        $this->effects = array();
        foreach($this->effectObjects as $effect) {
            $this->effects[] = [get_class($effect), $effect->export()];
        }
    }
}