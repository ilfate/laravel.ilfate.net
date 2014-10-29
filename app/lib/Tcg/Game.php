<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Game {

    /**
     * @var Player[]
     */
    public $players = array();
    public $maxPlayers = 2;

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

	public static function create()
    {
        $player1 = new Player(1);
        $player2 = new Player(2);

        $game = new Game();

        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->createLocations();

        $configs = \Config::get('tcg.cards');
        $cards = [
            Card::createFromConfig($configs[0]),
            Card::createFromConfig($configs[1]),
        ];
        foreach ($cards as $card) {
            $game->setUpCard(clone $card, $player1->id);
            $game->setUpCard(clone $card, $player1->id);
            $game->setUpCard(clone $card, $player2->id);
            $game->setUpCard(clone $card, $player2->id);
        }
        return $game;
    }

    public static function import($data)
    {
        $game = new Game();
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
            $game->addCard(Card::import($card));
        }
        $game->field = Field::import($data['field']);

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
        $game['decks'] = $decks;
        $game['hands'] = $hands;
        $game['graves'] = $graves;

        $game['field'] = $this->field->export();

        $cards = [];
        foreach ($this->cards as $card) {
            $cards[] = $card->export();
        }
        $game['cards'] = $cards;
        return $game;
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
        $this->field = new Field();
    }


}