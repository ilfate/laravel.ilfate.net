<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

abstract class Location {

	/**
	 * Player Id
	 *
	 * @var int
	 */
	public $owner;

	public $cards = array();

    public function __construct($playerId)
    {
        $this->owner = $playerId;
    }

    public static function import($data)
    {
        $location = new static($data['owner']);
        $location->cards = $data['cards'];

        return $location;
    }

	public function addCards(array $cards)
	{
		foreach ($cards as $card) {
			$this->cards[$card->id] = $card->id;
		}
	}

	public function count()
	{
		return count($this->cards);
	}

    public function export()
    {
        $location = [
            'cards' => $this->cards,
            'owner' => $this->owner
        ];
        return $location;
    }
}