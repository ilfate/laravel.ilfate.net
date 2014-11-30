<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Card {

    const CARD_LOCATION_DECK  = 1;
    const CARD_LOCATION_HAND  = 2;
    const CARD_LOCATION_FIELD = 3;
    const CARD_LOCATION_GRAVE = 4;

    const CARD_STATUS_CARD  = 0;
    const CARD_STATUS_UNIT  = 1;
    const CARD_STATUS_SPELL = 2;

    public static $locations = [
        Game::LOCATION_DECK  => 1,
        Game::LOCATION_HAND  => 2,
        Game::LOCATION_FIELD => 3,
        Game::LOCATION_GRAVE => 4,
    ];

    /**
     * @var Game
     */
    public $game;

    /**
     *  1 - deck
     *  2 - hand
     *  3 - field
     *  4 - grave
     *
     * @var int
     */
    public $location;

    /**
     * Player Id
     *
     * @var int
     */
    public $owner;
    /**
     * Ingame Id
     *
     * @var int
     */
    public $id;

    /**
     * @var Unit
     */
    public $unit;

    /**
     * @var Spell
     */
    public $spell;

    /**
     * @var int
     */
    public $card;

    public $isKing = false;

    public $config;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    public static function createFromConfig($config, Game $game, $isImport = false)
    {
        $card         = new Card($game);
        $card->card   = $config['card'];
        $card->config = $config;
        if (isset($config['isKing'])) {
            $card->isKing = true;
        }

        return $card;
    }

    public static function import($data, $game)
    {
        $config = \Config::get('tcg.cards.' .  $data['card']);
        $card         = Card::createFromConfig($config, $game, true);

        $card->id       = $data['id'];
        $card->owner    = $data['owner'];
        $card->location = $data['location'];
        $card->isKing   = $data['isKing'];

        if (!empty($data['unit'])) {
            $card->unit  = Unit::import($data['unit'], $config['unit'], $card);
            $card->spell = Spell::import($data['spell'], $config['spell'], $card);
        }

        return $card;
    }

    public function init()
    {
        if ($this->unit || $this->spell) {
            return;
        }
        $config = \Config::get('tcg.cards.' .  $this->card);
        $this->unit  = Unit::createFromConfig(\Config::get('tcg.units.' . $config['unit']), $this);
        $this->spell = Spell::createFromConfig(\Config::get('tcg.spells.' . $config['spell']), $this);
    }

    public function export()
    {
        $data = [
            'id'       => $this->id,
            'owner'    => $this->owner,
            'location' => $this->location,
            'card'     => $this->card,
            'isKing'   => $this->isKing,
        ];
        if ($this->unit && $this->spell) {
            $data['unit'] = $this->unit->export();
            $data['spell'] = $this->spell->export();
        }
        return $data;
    }

    public function render($playerId)
    {
        $data = [
            'id' => $this->id,
            'owner' => $this->owner,
        ];

        $data['unit'] = $this->unit->render($playerId);
        $data['spell'] = $this->spell->render();
        if ($this->config['image'] === (int) $this->config['image']) {
            // this is an image Id. We have to load image and author
            $imageConfig = \Config::get('tcgImages.images.' . $this->config['image']);
            $data['image'] = $imageConfig['url'];
            $author = \Config::get('tcgImages.authors.' . $imageConfig['author']);
            $data['imageAuthor'] = ['text' => $author['text'], 'id' => $imageConfig['author']];
        } else {
            $data['image'] = $this->config['image'];
            $data['imageAuthor'] = false;
        }


        return $data;
    }

    public function __clone()
    {
        if ($this->unit && $this->spell) {
            $this->unit = clone $this->unit;
            $this->unit->card = $this;
            $this->spell= clone $this->spell;
            $this->spell->card = $this;
        }
    }

}