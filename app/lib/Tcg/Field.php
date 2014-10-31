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

    /**
     * @var Game
     */
    public $game;

    public function __construct($players, Game $game)
    {
    	$this->players = $players;
        $this->game    = $game;
    }

    public function addCard(Card $card)
    {
    	$x = $card->unit->x;
    	$y = $card->unit->y;
        if ($x >= self::WIDTH || $y >= self::HEIGHT || $x < 0 || $y < 0) {
            throw new \Exception("Unit added to field has wrong X or Y", 1);    
        }
        if (isset($this->cards[$x][$y])) {
            throw new \Exception("You can't deploy on occupied cell", 12);        
        }
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

    /**
     * @return Card[]
     */
    public function getPlayerUnits($playerId)
    {
        $cards = [];
        foreach ($this->cards as $x => $col) {
            foreach ($col as $y => $cardId) {
                $card = $this->game->getCard($cardId);
                if ($card->owner == $playerId) {
                    $cards[] = $card;
                }
            }
        }
        return $cards;
    }

    public static function importField($data, $players, Game $game)
    {
        $location = new Field($players, $game);
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

    public function getTopPlayer()
    {
    	return $this->players[0];
    }

    public function addCards(array $cards)
    {
    	throw new Exception("Add Cards is not working for field", 1);
    }


    public function getRandomDeployCell($playerId)
    {
        $freeCells = $this->getFreeDeployCells($playerId);
        list($x, $y) = $freeCells[array_rand($freeCells)];
        if ($this->getTopPlayer() == $playerId) {
            list($x, $y) = $this->convert($x, $y);
        }
        return [$x, $y];
    }

    public function getFreeDeployCells($playerId)
    {
        if ($this->getTopPlayer() == $playerId) {
            $y1 = 0;
            $y2 = 1;
        } else {
            $y1 = self::HEIGHT - 2;
            $y2 = self::HEIGHT;
        }
        $freeCells = [];
        for ($x = 0; $x < self::WIDTH; $x++) {
            for ($y = $y1; $y < $y2; $y++) {
                if (!isset($this->cards[$x][$y])) {
                    $freeCells[] = [$x, $y];
                }
            }
        }
        return $freeCells;
    }

}