<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Support\Facades\Session;

class Card extends Eloquent implements RemindableInterface {

    use RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'cards';

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array();

    protected $appends = array();

    protected $guarded = array();

    public static function getMyCardsCount()
    {
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $myCardsCount = self::where('player_id', '=', $player->id)->count();
    }

    public static function getCardsInDeck($deckId)
    {
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $cards = self::where('player_id', '=', $player->id)
            ->where('deck_cards.deck_id', '=', $deckId)
            ->join('deck_cards', 'cards.id' , '=', 'deck_cards.card_id')
            ->get();
        return $cards;
    }

    public static function getMyKings()
    {
        $kingsIds = \Config::get('tcg.kingsIds');
        $player = User::getUser();
        if (!$player->id) {
            return false;
        }
        $cards = self::where('player_id', '=', $player->id)
            ->whereIn('card_id', $kingsIds)
            ->get();
        return $cards;
    }

    public static function getMyCardsForKing($kingId)
    {
        $kingConfig = \Config::get('tcg.cards.' . $kingId);
        $fraction = $kingConfig['fraction'];
        $cards = self::where('player_id', '=', Auth::user()->id)
            ->where('fraction', '=', $fraction)
            ->get();
        return $cards;
    }

    /**
     *
     */
    public static function prepareCardsForRender($cards, $options)
    {
        if (!$cards) {
            return [];
        }
        $cardsResult = [];
        foreach ($cards as $card) {
            $config = \Config::get('tcg.cards.' . $card->card_id);
            if (!empty($cardsResult[$card->card_id])) {
                // we will not render same cards
                $cardsResult[$card->card_id]['count'] ++;
                continue;
            }
            if (!empty($config['isKing']) && !empty($options['playable'])) {
                // king is not a playable card
                continue; 
            }
            if ($config['image'] === (int) $config['image']) {
                // this is an image Id. We have to load image and author
                $imageConfig = \Config::get('tcgImages.images.' . $config['image']);
                $image = $imageConfig['url'];
                $author = \Config::get('tcgImages.authors.' . $imageConfig['author']);
                $imageAuthor = ['text' => $author['text'], 'id' => $imageConfig['author']];
            } else {
                $image = $this->config['image'];
                $imageAuthor = false;
            }
            $cardsResult[$card->card_id] = [
                'id'     => $card->card_id,
                'config' => $config,
                'unit'   => \Config::get('tcg.units.' . $config['unit']),
                'spell'  => \Config::get('tcg.spells.' . $config['spell']),
                'image'  => $image,
                'author' => $imageAuthor,
                'count'  => 1
            ];
        }
        return $cardsResult;
    }

    public static function addCard($playerId, $cardId)
    {
        $card = new Card();
        $card->card_id = $cardId;
        $card->player_id = $playerId;

        $card->save();
    }

    public static function createDefaultKings($playerId)
    {
        $cardIds = \Config::get('tcg.defaultKingsIds');
        foreach ($cardIds as $cardId) {
            self::addCard($playerId, $cardId);
        }
    }

}
