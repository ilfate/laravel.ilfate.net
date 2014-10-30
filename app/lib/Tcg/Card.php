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

    const CARD_STATUS_CARD  = 0;
    const CARD_STATUS_UNIT  = 1;
    const CARD_STATUS_SPELL = 2;

    public static $locations = [
    	Game::LOCATION_DECK  => 1,
    	Game::LOCATION_HAND  => 2,
    	Game::LOCATION_FIELD => 3,
    	Game::LOCATION_GRAVE => 4,
    ];

    /**
     * @var Game
     */
    protected $game;

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

    /**
     * 0 - card
     * 1 - unit
     * 2 - spell
     * @var int
     */
    public $status = 0;

    public function __construct(Game $game)
    {
    	$this->game = $game;
    }


	public static function createFromConfig($config, Game $game)
	{
		$card = new Card($game);
        $card->card = $config['card'];
		$card->name = $config['name'];

		$card->unit  = Unit::createFromConfig(\Config::get('tcg.units.' . $config['unit']), $card);
		$card->spell = Spell::createFromConfig(\Config::get('tcg.units.' . $config['spell']), $card);

		return $card;
	}

	public static function import($data, $game)
	{
        $cardConfig = \Config::get('tcg.cards.' .  $data['card']);
        $card       = Card::createFromConfig($cardConfig, $game);

		$card->id       = $data['id'];
		$card->owner    = $data['owner'];
		$card->location = $data['location'];
		$card->status   = $data['status'];

		$card->unit  = Unit::import($data['unit'], $cardConfig['unit'], $card);
		$card->spell = Spell::import($data['spell'], $cardConfig['spell'], $card);

		return $card;
	}

	public function export()
	{
		$data = [
            'id'       => $this->id,
            'owner'    => $this->owner,
            'location' => $this->location,
            'card'     => $this->card,
            'status'   => $this->status,
		];
		$data['unit'] = $this->unit->export();
		$data['spell'] = $this->spell->export();
		return $data;
	}

	public function render($extData = array())
	{
		$data = [
			'id' => $this->id
		];
		switch ($this->status) {
			case 0:
				$renderType = 'card';
				// still a card
				
						
				$data['unit'] = $this->unit->render($renderType, $extData);
				$data['spell'] = $this->spell->render($renderType);
				break;
			
			default:
				# code...
				break;
		}
		$data['type'] = $renderType;
		$data['name'] = $this->name;

		return $data;
	}

}