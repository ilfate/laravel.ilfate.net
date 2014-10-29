<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Card {

    const CARD_LOCATION_DECK  = 1;
    const CARD_LOCATION_HAND  = 2;
    const CARD_LOCATION_FIELD = 3;
    const CARD_LOCATION_GRAVE = 4;

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

    /**
     * @var Unit
     */
    public $unit;

    /**
     * @var Spell
     */
    public $spell;

    /**
     * @var int
     */
    public $card;



	public static function createFromConfig($config)
	{
		$card = new Card();
        $card->card = $config['card'];
		$card->name = $config['name'];

		$card->unit  = Unit::createFromConfig($config['unit']);
		$card->spell = Spell::createFromConfig($config['spell']);

		return $card;
	}

	public static function import($data)
	{
        $cardConfig = \Config::get('tcg.cards.' .  $data['card']);
        $card       = Card::createFromConfig($cardConfig);

		$card->id = $data['id'];
		$card->owner = $data['owner'];
		$card->location = $data['location'];
		$card->unit = Unit::import($data['unit'], $cardConfig['unit']);
		$card->spell = Spell::import($data['spell'], $cardConfig['spell']);
		return $card;
	}

	public function export()
	{
		$data = [
            'id'       => $this->id,
            'owner'    => $this->owner,
            'location' => $this->location,
            'card'     => $this->card,
		];
		$data['unit'] = $this->unit->export();
		$data['spell'] = $this->spell->export();
		return $data;
	}

}