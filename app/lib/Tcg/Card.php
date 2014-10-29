<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Card {

	/**
	 *  1 - deck
	 *  2 - hand
	 *  3 - field
	 *  4 - grave
	 *
	 * @var int
	 */
	public $location;

	/**
	 * Player Id
	 *
	 * @var int
	 */
	public $owner;
	/**
	 * Ingame Id
	 *
	 * @var int
	 */
	public $id;

	public $name;
	public $unit;
	public $spell;



	public static function createFromConfig($config)
	{

		$config = [
			'name' => 'name',
			'unit' => [
				'totalHealth' => 10
			],
			'spell' => [],
		];

		$card = new Card();
		$card->name = $config['name'];

		$card->unit  = Unit::createFromConfig($config['unit']);
		$card->spell = Spell::createFromConfig($config['spell']);

		return $card;
	}

	public function import($data)
	{
		$data = [
			'id' => 1,
			'owner' => 1,
			'location' => 1,
			'unit' => [
				'currentHealth' => 9
			],
			'spell' => [],
		];

		$this->id = $data['id'];
		$this->owner = $data['owner'];
		$this->location = $data['location'];
		$this->unit->import($data['unit']);
		$this->spell->import($data['spell']);
		return $this;
	}

	public function export()
	{
		$data = [

		];
		$data
		return $data;
	}

}