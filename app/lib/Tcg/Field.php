<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Field extends Location {

	const WIDTH = 5;
	const HEIGHT = 5;

	/**
	 * First player is on top.
	 *
	 * @var array
	 */
	public $players;

    public function __construct($players)
    {
    	$this->players = $players;
    }

    public function addCard(Card $card)
    {
    	$x = $card->unit->x;
    	$y = $card->unit->y;
    	if (!isset($this->cards[$x])) {
    		$this->cards[$x] = [];
    	}
    	$this->cards[$x][$y] = $card->id;
    }

    public function render($playerId)
    {
    	$data = [
    		'width'  => self::WIDTH,
    		'height' => self::HEIGHT,
    		'cards'  => [],
    	];

    	foreach ($this->cards as $x => $col) {
    		foreach ($col as $y => $cardId) {
    			if ($this->getTopPlayer() == $playerId) {
    				list($x, $y) = $this->convert($x, $y);
    			}
    			$data['cards'][] = [$cardId, $x, $y];
    		}
    	}
    	return $data;
    }

    public static function importField($data, $players)
    {
        $location = new Field($players);
        $location->cards = $data['cards'];

        return $location;
    }

    public function convertCoordinats($card)
    {
    	if ($this->getTopPlayer() == $card->owner) {
    		// yes we need to switch for top player
    		$converted = $this->convert($card->unit->x, $card->unit->y);
    		$card->unit->x = $converted[0];
    		$card->unit->y = $converted[1];
    	}
    }

    public function removeUnit($card)
    {
    	$x = $card->unit->x;
    	$y = $card->unit->y;
    	unset($this->cards[$x][$y]);
    }

    protected function convert($x, $y)
    {
        // 0 -> 4, 1 -> 3, 2 -> 2, 3 -> 1, 4 -> 0
    	$x = (self::WIDTH - 1) - $x;
    	$y = (self::HEIGHT - 1) - $y;
    	return [$x, $y];
    }

    protected function getTopPlayer()
    {
    	return $this->players[0];
    }

    public function addCards(array $cards)
    {
    	throw new Exception("Add Cards is not working for field", 1);
    }

}