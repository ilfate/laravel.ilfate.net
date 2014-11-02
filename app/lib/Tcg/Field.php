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

    /** @var array */
    public $map = array();

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
        if (isset($this->map[$x][$y])) {
            throw new \Exception("You can't deploy on occupied cell", 12);        
        }
    	if (!isset($this->map[$x])) {
    		$this->map[$x] = [];
    	}
    	$this->map[$x][$y] = $card->id;
        if (!isset($this->cards[$card->owner])) {
            $this->cards[$card->owner] = [];
        }
        $this->cards[$card->owner][] = $card->id;
    }

    public function render($playerId, $isBattle)
    {
    	$data = [
    		'width'  => self::WIDTH,
    		'height' => self::HEIGHT,
    		'cards'  => [],
    	];
    	foreach ($this->map as $x => $col) {
    		foreach ($col as $y => $cardId) {
                list($x1, $y1) = [$x, $y];
    			if ($this->getTopPlayer() == $playerId) {
    				list($x1, $y1) = $this->convert($x, $y);

    			}
    			$data['cards'][] = [$cardId, $x1, $y1];
    		}
    	}
        if ($isBattle) {
            $data['order'] = $this->cards[$playerId];
        }
    	return $data;
    }

    /**
     * @return int[]
     */
    public function getPlayerUnits($playerId)
    {
        return $this->cards[$playerId];
    }

    public static function importField($data, $players, Game $game)
    {
        $location = new Field($players, $game);
        $location->map   = $data['map'];
        $location->cards = $data['cards'];

        return $location;
    }

    public function export()
    {
        $location = [
            'cards' => $this->cards,
            'map'   => $this->map,
        ];
        return $location;
    }

    public function convertCoordinats($x, $y, $playerId)
    {
    	if ($this->getTopPlayer() == $playerId) {
    		// yes we need to switch for top player
    		return $this->convert($x, $y);
    	}
        return [$x, $y];
    }

    public function removeUnit($card)
    {
    	$x = $card->unit->x;
    	$y = $card->unit->y;
    	unset($this->map[$x][$y]);
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

    public function getNextCard($playerId, $cardId = null)
    {
        $next = false;
        foreach ($this->cards[$playerId] as $card) {
            if ($cardId === null || $next) {
                return $card;
            }
            if ($card == $cardId) {
                $next = true;
            }
        }
        return null;
    }

    public function moveUnit(Card $card, $x, $y)
    {
        if ($x >= self::WIDTH || $x < 0 || $y >= self::HEIGHT || $y < 0) {
            throw new \Exception("unit moved out of the field");
        }
        list($x, $y) = $this->convertCoordinats($x, $y, $card->owner);
        if (isset($this->map[$x][$y])) {
            throw new \Exception('Cant move to occupied cell');
        }
        $oldX = $card->unit->x;
        $oldy = $card->unit->y;
        if ($oldX != $x && $oldy != $y) {
            throw new \Exception('Unit can move only on close cell');
        }
        $card->unit->move($x, $y);
        $this->map[$x][$y] = $card->id;
        unset($this->map[$oldX][$oldy]);
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

    public function getDistance($x1, $y1, $x2, $y2)
    {
        if ($x1 == $x2) {
            return abs($y1 - $y2);
        } else if ($y1 == $y2) {
            return abs($x1 - $x2);
        } else {
            return sqrt(pow(abs($y1 - $y2), 2) + pow(abs($x1 - $x2), 2));
        }
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
                if (!isset($this->map[$x][$y])) {
                    $freeCells[] = [$x, $y];
                }
            }
        }
        return $freeCells;
    }
}