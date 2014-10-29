<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Deck {

	/**
	 * Player Id
	 *
	 * @var int
	 */
	public $owner;

	public $cards = array();


	public function addCards(array $cards)
	{
		foreach ($cards as $card) {
			$this->cards[$card->id] = $card;
		}
	}

	public function getCard($id)
	{
		if (!empty($this->cards[$id])) {
			return $this->cards[$id];
		} else {
			throw new \Exception('Card with id = ' . $id . ' not found in deck');
		}
	}

	public function count()
	{
		return count($this->cards);
	}
}