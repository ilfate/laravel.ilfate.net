<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Field extends Location {

    const MAP_TYPE_EMPTY = 'empty';
    const MAP_TYPE_RANDOM = 'random';
    const MAP_TYPE_FIXED = 'fixed';

    /**
     * @var Game
     */
    public $game;

    /** @var array */
    public $map = array();

    /**
     * @var array objectIds
     */
    public $objectMap = [];
    /**
     * @var FieldObject[]
     */
    public $objects = [];

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public function init()
    {
        $mapType = $this->game->config['mapType'];
        switch ($mapType) {
            case self::MAP_TYPE_EMPTY:

                break;
            case self::MAP_TYPE_FIXED:
                $configMap = \Config::get('tcg.fieldMap.1');
                foreach ($configMap as $configWall) {
                    $fieldObject = FieldObject::createFromConfig($configWall['id'], $this);
                    $fieldObject->x = $configWall['x'];
                    $fieldObject->y = $configWall['y'];
                    $this->addObject($fieldObject, false);
                }

                break;
        }
    }

    public static function importField($data, Game $game)
    {
        $location = new Field($game);

        $location->map         = $data['map'];
        $location->cards       = $data['cards'];
        foreach ($data['objects'] as $object) {
            $fieldObject = FieldObject::import($object, $location);
            $location->addObject($fieldObject, false);
        }

        return $location;
    }

    public function export()
    {
        $objects = $this->exportObjects();
        $location = [
            'cards'       => $this->cards,
            'map'         => $this->map,
            'objects'     => $objects,
        ];
        return $location;
    }

    public function addCard(Card $card)
    {
        $x = $card->unit->x;
        $y = $card->unit->y;
        if ($x >= Game::WIDTH || $y >= Game::HEIGHT || $x < 0 || $y < 0) {
            throw new \Exception("Unit added to field has wrong X or Y", 1);
        }
        if (!isset($this->map[$x])) {
            $this->map[$x] = [];
        }

        $this->map[$x][$y] = $card->id;

        $this->cards[] = $card->id;
    }

    public function render($playerId, $isBattle)
    {
        $objects = [];
        foreach ($this->objects as $fieldObject) {
            $objects[] = $fieldObject->render($playerId);
        }
        $data = [
            'width'  => Game::WIDTH,
            'height' => Game::HEIGHT,
            'cards'  => $this->cards,
            'objects' => $objects,
        ];

        return $data;
    }

    public function exportObjects()
    {
        $data = [];
        foreach ($this->objects as $object)
        {
            $data[] = $object->export();
        }
        return $data;
    }

    public function addObject(FieldObject $object, $isAmnimation = true)
    {
        if (!$object->id) {
            $this->objects[] = $object;
            end($this->objects);
            $newId = key($this->objects);
            $object->id = $newId;
        } else {
            $this->objects[$object->id] = $object;
        }
        $this->objectMap[$object->x][$object->y] = $object->id;
        if ($isAmnimation) {
            $this->game->log->logFieldObject('create', $object->id);
        }
    }

    public function removeObject($objectId)
    {
        $object = $this->getObject($objectId);
        $this->game->log->logFieldObject('remove', $object->id);
        unset($this->objectMap[$object->x][$object->y]);
        unset($this->objects[$object->id]);
    }

    public function getObject($objectId)
    {
        return $this->objects[$objectId];
    }
    public function findObject($x, $y)
    {
        if (isset($this->objectMap[$x][$y])) {
            return $this->objects[$this->objectMap[$x][$y]];
        }
        return false;
    }

    public function removeUnit(Card $card)
    {
        $x = $card->unit->x;
        $y = $card->unit->y;
        unset($this->map[$x][$y]);

        $key = array_search($card->id, $this->cards);
        unset($this->cards[$key]);
        $this->cards = array_diff( $this->cards, array( null ) );
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
        if ($x >= Game::WIDTH || $x < 0 || $y >= Game::HEIGHT || $y < 0) {
            throw new \Exception("unit moved out of the field");
        }
        list($x, $y) = $this->game->convertCoordinats($x, $y, $card->owner);
        if (isset($this->map[$x][$y])) {
            throw new \Exception('Cant move to occupied cell');
        }
        if (isset($this->objectMap[$x][$y])) {
            $object = $this->getObject($this->objectMap[$x][$y]);
            if (!$object->isPassable()) {
                throw new Exception('Cant move to wall object');
            }
        }
        $oldX = $card->unit->x;
        $oldy = $card->unit->y;

        $leftSteps = $card->unit->checkIsUnitAbleToMove($x, $y) - 1;
        $card->unit->move($x, $y);
        $this->map[$x][$y] = $card->id;
        unset($this->map[$oldX][$oldy]);
        $card->unit->afterMove();
        return $leftSteps;
    }

    public function getRandomDeployCell($playerId)
    {
        $freeCells = $this->getFreeDeployCells($playerId);
        list($x, $y) = $freeCells[array_rand($freeCells)];
        if ($this->game->isTopPlayer($playerId)) {
            list($x, $y) = $this->game->convert($x, $y);
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
        if ($this->game->isTopPlayer($card->owner)) {
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
                if (($x == $dx && $y == $dy) || $dx < 0 || $dy < 0 || $dx >= Game::WIDTH || $dy >= Game::HEIGHT) {
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
        if ($this->game->isTopPlayer($playerId)) {
            $y1 = 0;
            $y2 = 1;
        } else {
            $y1 = Game::HEIGHT - 2;
            $y2 = Game::HEIGHT;
        }
        $freeCells = [];
        for ($x = 0; $x < Game::WIDTH; $x++) {
            for ($y = $y1; $y < $y2; $y++) {
                if (!isset($this->map[$x][$y])) {
                    $freeCells[] = [$x, $y];
                }
            }
        }
        return $freeCells;
    }

    public function isDeployable($x, $y)
    {
        if (isset($this->map[$x][$y])) {
            return false;
        }
        if (isset($this->objectMap[$x][$y])) {
            $object = $this->getObject($this->objectMap[$x][$y]);
            if (!$object->isPassable()) {
                return false;
            }
        }
        return true;
    }

    public function isEachPlayerPlayedEnoughCards()
    {
        $min = $this->game->config['minimumCardsInGame'];
        $cards = [];
        foreach ($this->cards as $cardId) {
            $card = $this->game->getCard($cardId);
            if (!isset($cards[$card->owner])) {
                $cards[$card->owner] = 0;
            }
            $cards[$card->owner] ++;
        }
        foreach ($this->game->players as $playerId => $player) {
            if (!isset($cards[$playerId]) || $cards[$playerId] < $min) {
                return false;
            }
        }
        return true;
    }
}