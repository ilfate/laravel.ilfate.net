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

    protected static $exportValues = array(
        'phase',
        'turnNumber',
        'playerTurnId',
        'currentCardId',
        'gameResult',
        'spellsPlayed',
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
            throw new \Exception('Card with id ' . $id. ' not found in game');
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

    protected function setUpCard(Card $card, $playerId)
    {
        $newId          = count($this->cards);
        $card->id       = $newId;
        $card->owner    = $playerId;
        $card->location = Card::CARD_LOCATION_DECK;
        $this->cards[]  = $card;

        $this->decks[$playerId]->addCards([$card]);
    }

    protected function createLocations()
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


}