<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

use ClassPreloader\Config;

class Unit {

    const RENDER_TYPE_UNIT = 'unit';
    const RENDER_TYPE_CARD = 'card';

    const CONFIG_VALUE_TOTAL_HEALTH = 'totalHealth';
    const CONFIG_VALUE_TEXT         = 'text';


    /**
     * totalHealth
     * text
     *
     * @var array
     */
	public $config;

	public $currentHealth;
    public $x;
    public $y;

    /** 
     * @var Card
     */
    protected $card;

    public $effects = array();


    /**
     * @var Effect\Effect[]
     */
    protected $effectObjects = array();

	public static function createFromConfig($config, Card $card)
	{
		$unit = new Unit();
		$unit->config = $config;
        $unit->card   = $card;

		return $unit;
	}

	public static function import($data, $unitId, $card)
	{
        $unit = Unit::createFromConfig(\Config::get('tcg.units.' . $unitId), $card);
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

    public function render($type, $extData)
    {
        $data = [
            'config' => $this->config,
        ];
        $data['x'] = empty($extData['x']) ? $this->x : $extData['x'];
        $data['y'] = empty($extData['y']) ? $this->y : $extData['y'];
        if ($type == self::RENDER_TYPE_UNIT) {
            $data['currentHealth'] = $this->currentHealth;
        }
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