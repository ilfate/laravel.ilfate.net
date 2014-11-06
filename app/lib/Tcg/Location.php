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

    public function addCardIds(array $cards)
    {
        foreach ($cards as $cardId) {
            $this->cards[$cardId] = $cardId;
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

    public function getRandom($num = 1)
    {
        return array_rand($this->cards, $num);
    }

    public function remove($cardIds = array())
    {
        foreach ($cardIds as $cardId) {
            unset($this->cards[$cardId]);
        }
    }

    public function hasCard($cardId) 
    {
        return !empty($this->cards[$cardId]);
    }
}