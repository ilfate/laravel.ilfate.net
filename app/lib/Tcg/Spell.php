<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Spell {

    const CONFIG_VALUE_TEXT   = 'text';

    /**
     * @var array
     */
    public $config;

    /** 
     * @var Card
     */
    public $card;

	public static function createFromConfig($config, $card)
	{
		$spell = new Spell();
        $spell->config = $config;
        $spell->card = $card;

		return $spell;
	}

	public static function import($data, $spellId, $card)
	{
        $spell = Spell::createFromConfig(\Config::get('tcg.spells.' . $spellId), $card);
		return $spell;
	}

    public function export()
    {
        $data = [

        ];
        return $data;
    }

    public function render()
    {
        $data = [
            'config' => $this->config
        ];

        return $data;
    }
}