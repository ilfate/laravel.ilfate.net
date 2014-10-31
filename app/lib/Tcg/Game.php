<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Game {

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

    /**
     * @var Player[]
     */
    public $players = array();
    public $maxPlayers = 2;

    public $phase = 0;
    public $playerTurnId;
    public $currentPlayerId;
    public $turnNumber = 0;

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

    /**
     * @var Card[]
     */
    public $cards = array();

    public function __construct($currentPlayerId)
    {
        $this->currentPlayerId = $currentPlayerId;
    }

    /**
     * @return Game
     */
	public static function create($currentPlayerId)
    {
        $player1 = new Player($currentPlayerId);
        $player2 = new Player(2);
        $player2->type = Player::PLAYER_TYPE_BOT;

        $game = new Game($currentPlayerId);

        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->createLocations();

        $configs = \Config::get('tcg.cards');
        $cards = [
            Card::createFromConfig($configs[0], $game),
            Card::createFromConfig($configs[1], $game),
        ];
        foreach ($cards as $card) {
            $game->setUpCard(clone $card, $player1->id);
            $game->setUpCard(clone $card, $player1->id);
            $game->setUpCard(clone $card, $player2->id);
            $game->setUpCard(clone $card, $player2->id);
        }
        return $game;
    }

    /**
     * @return Game
     */
    public static function import($data, $currentPlayerId)
    {
        $game = new Game($currentPlayerId);
        foreach ($data['players'] as $player) {
            $game->addPlayer(Player::import($player));
        }
        foreach ($data['decks'] as $deck) {
            $game->addDeck(Deck::import($deck));
        }
        foreach ($data['hands'] as $hand) {
            $game->addHand(Hand::import($hand));
        }
        foreach ($data['graves'] as $grave) {
            $game->addGrave(Grave::import($grave));
        }
        foreach ($data['cards'] as $card) {
            $game->addCard(Card::import($card, $game));
        }
        $game->field = Field::importField($data['field'], array_keys($game->players), $game);

        $game->phase        = $data['phase'];
        $game->turnNumber   = $data['turnNumber'];
        $game->playerTurnId = $data['playerTurnId'];

        return $game;
    }

    public function export()
    {
        $game    = [];
        $players = [];
        $decks   = [];
        $hands   = [];
        $graves  = [];
        foreach ($this->players as $playerId => $player) {
            $players[] = $player->export();
            $decks[] = $this->decks[$playerId]->export();
            $hands[] = $this->hands[$playerId]->export();
            $graves[] = $this->graves[$playerId]->export();
        }
        $game['players'] = $players;
        $game['decks']   = $decks;
        $game['hands']   = $hands;
        $game['graves']  = $graves;

        $game['turnNumber']   = $this->turnNumber;
        $game['phase']        = $this->phase;
        $game['playerTurnId'] = $this->playerTurnId;

        $game['field'] = $this->field->export();

        $cards = [];
        foreach ($this->cards as $card) {
            $cards[] = $card->export();
        }
        $game['cards'] = $cards;
        return $game;
    }

    public function render($playerId)
    {
        //var_dump($this->field); die;
        $data = [
            'template' => \Config::get('tcg.game.template.' . $this->phase),
            'turn'     => $this->playerTurnId,
            'js'       => [
                'phase' => $this->phase,
            ]
        ];
        $data['hand']  = $this->renderHand($playerId);
        $data['field'] = $this->renderField($playerId);
        $data['opponentHand'] = $this->renderOpponentHand($playerId);
        return $data;
    }

    public function action($name, $data = []) 
    {
        switch ($name) {
            case self::GAME_ACTION_DEPLOY:
                $this->deploy($data['cardId'], $data['x'], $data['y']);
                break;

            case self::GAME_ACTION_SKIP:
                $this->players[$this->currentPlayerId]->skippedTurn = true;
                $this->nextTurn();
                break;
            
            default:
                # code...
                break;
        }
    }

    public function deploy($cardId, $x, $y, $isBotAction = false)
    {
        $card = $this->getCard($cardId);
        if (!$isBotAction && $this->currentPlayerId != $card->owner) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is tring to deploy not his card", 1);
        }
        if ($y < Field::HEIGHT - 2) {
            throw new \Exception("This field is forbidden for deploy", 1);    
        }
        $card->unit->x = $x;
        $card->unit->y = $y;
        $this->field->convertCoordinats($card);
        $this->moveCards([$card], self::LOCATION_HAND, self::LOCATION_FIELD);
        $this->players[$this->currentPlayerId]->skippedTurn = false;

        $this->nextTurn();
    }

    public function gameAutoActions()
    {
        switch ($this->phase) {
            case self::PHASE_GAME_NOT_STARTED:
                // the game is just created
                $handSize = \Config::get('tcg.game.handDraw');
                foreach ($this->players as $playerId => $player) {
                    $this->drawCards($playerId, $handSize);
                }
                $this->phase = self::PHASE_UNIT_DEPLOYING;
                $this->playerTurnId = array_rand($this->players);
                
                if ($this->players[$this->playerTurnId]->type == Player::PLAYER_TYPE_BOT) {
                    $this->gameAutoActions();
                }
                break;

            case self::PHASE_UNIT_DEPLOYING:
                if ($this->players[$this->playerTurnId]->type == Player::PLAYER_TYPE_BOT && $this->hands[$this->playerTurnId]->count() > 0) {
                    $cardId = $this->hands[$this->playerTurnId]->getRandom();
                    list($x, $y) = $this->field->getRandomDeployCell($this->playerTurnId);
                    $this->deploy($cardId, $x, $y, true);
                }
                $playersFinished = 0;
                foreach ($this->players as $id => $player) {
                    if ($player->skippedTurn || $this->hands[$id]->count() == 0) {
                        $playersFinished++;
                    }
                }
                if ($playersFinished == count($this->players)) {
                    // GO to next phase!!
                    $this->startBattle();
                }
                break;

        }
    }

    public function nextTurn()
    {
        $nextOne = false;
        foreach ($this->players as $id => $player) {
            if ($nextOne) {
                $this->playerTurnId = $id;
                return;    
            }
            if ($id == $this->playerTurnId) {
                $nextOne = true;
            }
        }
        reset($this->players);
        $this->playerTurnId = key($this->players);
        
    }

    protected function startBattle()
    {
        $this->phase = self::PHASE_BATTLE;
        foreach ($this->players as $id => $player) {
            $this->drawCards($id, \Config::get('tcg.game.spellsDraw'));
        }
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
    }
    public function addCard(Card $card)
    {
        $this->cards[$card->id] = $card;
    }

    public function drawCards($playerId, $num = 1)
    {
        if ($this->decks[$playerId]->count() < 1) {
            throw new \Exception("No card to draw from deck", 1);
        }
        $cardsIds = $this->decks[$playerId]->getRandom($num);
        $cards = [];
        foreach ($cardsIds as $cardId) {
            $cards[] = $this->cards[$cardId];
        }
        $this->moveCards($cards, self::LOCATION_DECK, self::LOCATION_HAND);
    }

    public function moveCards($cards, $from, $to)
    {
        foreach ($cards as $card) {
            if ($from == self::LOCATION_FIELD) {
                $this->{$from}->removeUnit($card->id);
            } else {
                $this->{$from}[$card->owner]->remove([$card->id]);
            }
            if ($to == self::LOCATION_FIELD) {
                $this->{$to}->addCard($card);
            } else {
                $this->{$to}[$card->owner]->addCards([$card]);
            }
            $card->location = Card::$locations[$to];
        }
    }

    protected function renderField($playerId)
    {
        $data = $this->field->render($playerId);
        foreach ($data['cards'] as $cardData) {
            if (!isset($data['map'][$cardData[1]])) {
                $data['map'][$cardData[1]] = [];
            }
            $data['map'][$cardData[1]][$cardData[2]] = $this->cards[$cardData[0]]->render(['x' => $cardData[1], 'y' => $cardData[2]]);
        }
        unset($data['cards']);
        return $data;
    }

    protected function renderHand($playerId)
    {
        $data = [];
        $cards = $this->hands[$playerId]->cards;
        foreach ($cards as $cardId) {
            $data[] = $this->cards[$cardId]->render();
        }
        return $data;
    }

    protected function renderOpponentHand($playerId)
    {
        foreach ($this->players as $id => $player) {
            if ($playerId != $id) {
                // here our enemy
                return ['size' => $this->hands[$id]->count()];
            }
        }
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
        }
        $this->field = new Field(array_keys($this->players), $this);
    }


}