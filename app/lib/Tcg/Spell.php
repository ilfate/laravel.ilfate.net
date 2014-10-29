<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Spell {

    public $type;


	public static function createFromConfig($config)
	{
		$spell = new Spell();
        $spell->type = $config['type'];

		return $spell;
	}

	public static function import($data, $spellId)
	{
        $spell = Spell::createFromConfig(\Config::get('tcg.spells.' . $spellId));
		return $spell;
	}

    public function export()
    {
        $data = [

        ];
        return $data;
    }
}