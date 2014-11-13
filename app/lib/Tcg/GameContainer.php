<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class GameContainer {

    const PHASE_GAME_NOT_STARTED = 0;
    const PHASE_HAND_DRAW_1      = 1;
    const PHASE_HAND_DRAW_2      = 2;
    const PHASE_UNIT_DEPLOYING   = 3;
    const PHASE_BATTLE           = 4;
    const PHASE_GAME_END         = 5;

    const WIDTH = 5;
    const HEIGHT = 5;

    const LOCATION_DECK  = 'decks';
    const LOCATION_HAND  = 'hands';
    const LOCATION_FIELD = 'field';
    const LOCATION_GRAVE = 'graves';

    const GAME_ACTION_DEPLOY = 'deploy';
    const GAME_ACTION_SKIP   = 'skip';
    const GAME_ACTION_MOVE   = 'move';
    const GAME_ACTION_CAST   = 'cast';

    const GAME_RESULT_DRAW   = 'draw';
    const GAME_RESULT_WIN    = 'win';
    const GAME_RESULT_LOOSE  = 'loose';

    const EVENT_UNIT_GET_DAMAGE  = 'unit_get_damage';
    const EVENT_UNIT_DEAL_DAMAGE = 'unit_deal_damage';
    const EVENT_UNIT_DEATH       = 'unit_death';
    const EVENT_UNIT_MOVE        = 'unit_move';
    const EVENT_UNIT_DEPLOY      = 'unit_deploy';

    const EVENT_TRIGGER_BEFORE_UNIT_MOVE = 'before_unit_move';
    const EVENT_TRIGGER_AFTER_UNIT_MOVE = 'after_unit_move';
    const EVENT_TRIGGER_UNIT_MOVE_TO_CELL = 'unit_move_to_cell';
    const EVENT_TRIGGER_UNIT_MOVE_FROM_CELL = 'unit_move_from_cell';
    const EVENT_TRIGGER_UNIT_DEPLOY = 'unit_deploy';
    const EVENT_TRIGGER_UNIT_DEPLOY_TO_CELL = 'unit_deploy_to_cell';
    const EVENT_TRIGGER_UNIT_DEATH = 'unit_death';

    const IMPORT_TYPE_NORMAL = 'normal';
    const IMPORT_TYPE_UPDATE = 'update';

    protected static $exportValues = array(
        'phase',
        'turnNumber',
        'playerTurnId',
        'currentCardId',
        'gameResult',
        'spellsPlayed',
        'events',
        'eventsExpire',
    );

    /**
     * @var Player[]
     */
    public $players = array();
    public $teams   = array();
    public $maxPlayers = 2;

    public $phase = 0;
    public $playerTurnId;
    public $currentPlayerId;
    public $currentCardId;
    public $turnNumber = 0;
    public $spellsPlayed = [];

    /**
     * @var Deck[]
     */
    public $decks = array();
    /**
     * @var Hand[]
     */
    public $hands = array();
    /**
     * @var Grave[]
     */
    public $graves = array();
    /**
     * @var Field
     */
    public $field;

    public $gameResult;

    /**
     * @var GameLog
     */
    public $log;

    public $gameType = 'battle';

    public $data;
    public $sessionType;



    /**
     * @var Card[]
     */
    public $cards = array();

    public function __construct($currentPlayerId)
    {
        $this->currentPlayerId = $currentPlayerId;
    }

    public function getCard($id)
    {
        if (empty($this->cards[$id])) {
            if ($this->sessionType == self::IMPORT_TYPE_UPDATE) {
                if (empty($this->data['cards'][$id])) {
                    throw new \Exception('During update we are trying to import card with id=' . $id. ', but it is missing');    
                }
                $this->addCard(Card::import($this->data['cards'][$id], $this));
                return $this->cards[$id];
            } else {
                throw new \Exception('Card with id ' . $id. ' not found in game');
            }
        }
        return $this->cards[$id];
    }

    public function addDeck(Deck $deck)
    {
        $this->decks[$deck->owner] = $deck;
    }
    public function addHand(Hand $hand)
    {
        $this->hands[$hand->owner] = $hand;
    }
    public function addGrave(Grave $grave)
    {
        $this->graves[$grave->owner] = $grave;
    }
    public function addPlayer(Player $player)
    {
        $this->players[$player->id] = $player;
        if (count($this->players) == 1) {
            $player->isTopPlayer = true;
        }
        if (!isset($this->teams[$player->team])) {
            $this->teams[$player->team] = [];
        }
        $this->teams[$player->team][] = $player->id;
    }
    public function getAllPlayerEnemies($playerId)
    {
        $enemies = [];
        foreach ($this->teams as $team) {
            $key = array_search($playerId, $team);
            if ($key === false) {
                $enemies = array_merge($enemies, $team);
            }
        }
        return $enemies;
    }
    public function addCard(Card $card)
    {
        $this->cards[$card->id] = $card;
    }

    protected function isItBotTurn()
    {
        return $this->players[$this->playerTurnId]->type == Player::PLAYER_TYPE_BOT;
    }

    public function setUpCard(Card $card, $playerId)
    {
        $newId          = count($this->cards);
        $card->id       = $newId;
        $card->owner    = $playerId;
        $card->location = Card::CARD_LOCATION_DECK;
        $this->cards[]  = $card;

        $this->decks[$playerId]->addCards([$card]);
    }

    public function createLocations()
    {
        foreach ($this->players as $player)
        {
            $this->decks[$player->id] = new Deck($player->id);
            $this->hands[$player->id] = new Hand($player->id);
            $this->graves[$player->id] = new Grave($player->id);
            $this->spellsPlayed[$player->id] = 0;
        }
        $this->field = new Field(array_keys($this->players), $this);
    }

    public function isTopPlayer($playerId)
    {
        return $this->getPlayer($playerId)->isTopPlayer;
    }

    public function getPlayer($playerId)
    {
        return $this->players[$playerId];
    }

    public function convertCoordinats($x, $y, $playerId)
    {
        if ($this->isTopPlayer($playerId)) {
            // yes we need to switch for top player
            return $this->convert($x, $y);
        }
        return [$x, $y];
    }

    public function convert($x, $y)
    {
        // 0 -> 4, 1 -> 3, 2 -> 2, 3 -> 1, 4 -> 0
        $x = (self::WIDTH - 1) - $x;
        $y = (self::HEIGHT - 1) - $y;
        return [$x, $y];
    }
}