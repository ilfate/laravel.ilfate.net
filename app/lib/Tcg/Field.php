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

    public $playerUnits = array();

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
        if (!isset($this->playerUnits[$card->owner])) {
            $this->playerUnits[$card->owner] = [];
        }
        $this->playerUnits[$card->owner][] = $card->id;
        $this->cards[] = $card->id;
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
//                list($x1, $y1) = [$x, $y];
//    			if ($this->getTopPlayer() == $playerId) {
//    				list($x1, $y1) = $this->convert($x, $y);
//
//    			}
    			$data['cards'][] = $cardId;
    		}
    	}
        if ($isBattle) {
            $data['order'] = $this->cards;
        }
    	return $data;
    }

    /**
     * @return int[]
     */
    public function getPlayerUnits($playerId)
    {
        return $this->playerUnits[$playerId];
    }

    public static function importField($data, $players, Game $game)
    {
        $location = new Field($players, $game);

        $location->map         = $data['map'];
        $location->cards       = $data['cards'];
        $location->playerUnits = $data['playerUnits'];

        return $location;
    }

    public function export()
    {
        $location = [
            'cards'       => $this->cards,
            'playerUnits' => $this->playerUnits,
            'map'         => $this->map,
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

    public function removeUnit(Card $card)
    {
    	$x = $card->unit->x;
    	$y = $card->unit->y;
    	unset($this->map[$x][$y]);

        $key = array_search($card->id, $this->playerUnits[$card->owner]);
        unset($this->playerUnits[$card->owner][$key]);
        $this->playerUnits[$card->owner] = array_diff( $this->playerUnits[$card->owner], array( null ) );

        $key = array_search($card->id, $this->cards);
        unset($this->cards[$key]);
        $this->cards = array_diff( $this->cards, array( null ) );
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

    public function getNextCard($cardId = null)
    {
        $next = false;
        foreach ($this->cards as $card) {
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
        $leftSteps = $card->unit->move($x, $y);
        $this->map[$x][$y] = $card->id;
        unset($this->map[$oldX][$oldy]);
        $card->unit->afterMove();
        return $leftSteps;
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

    public function getNeibourUnit($card, $dx, $dy) {
        list($x, $y) = $this->getRelativeCoordinats($dx, $dy, $card);
        if (isset($this->map[$x][$y])) {
            return $this->game->cards[$this->map[$x][$y]];
        }
        return false;
    }

    public function getRelativeCoordinats($dx, $dy, $card)
    {
        if ($this->getTopPlayer() == $card->owner) {
            // this is top player we need to switch
            $dx = -$dx;
            $dy = -$dy;
        }
        $x = $card->unit->x + $dx;
        $y = $card->unit->y + $dy;
        return [$x, $y];
    }

    public function getAllPlayersUnitsInRange($x, $y, $range, array $playerIds)
    {
        $result = [];
        for($dx = $x - $range; $dx <= $x + $range; $dx++) {
            for($dy = $y - $range; $dy <= $y + $range; $dy++) {
                if (($x == $dx && $y == $dy) || $dx < 0 || $dy < 0 || $dx >= self::WIDTH || $dy >= self::HEIGHT) {
                    continue;
                }
                if (isset($this->map[$dx][$dy])) {
                    $cardId = $this->map[$dx][$dy];
                    $card = $this->game->getCard($cardId);
                    if (in_array($card->owner, $playerIds)) {
                        $result[] = $card;
                    }
                }
            }
        }
        return $result;
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