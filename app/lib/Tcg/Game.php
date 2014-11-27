<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Game extends GameContainer {

    use Events;
    use Sockets;

    public function init()
    {
        $this->setKings();
    }

    /**
     * @return Game
     */
    public static function import($type, $data, $currentPlayerId)
    {
        $game = new Game($currentPlayerId);
        $game->sessionType = $type;

        foreach (self::$exportValues as $valueName) {
            $game->{$valueName} = $data[$valueName];
        }

        $game->setUpGameObject();

        foreach ($data['players'] as $player) {
            $game->addPlayer(Player::import($player));
        }

        if ($type == self::IMPORT_TYPE_NORMAL) {

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
            $game->field = Field::importField($data['field'], $game);
        } else if($type == self::IMPORT_TYPE_UPDATE) {
            $game->data = $data;
        }

        $game->log   = GameLog::import($data['log'], $game);

        $game->start();
        return $game;
    }

    public function setUpGameObject()
    {
        $this->config = \Config::get('tcg.game.' . $this->gameType);
    }

    public function start()
    {
        $this->initSockets();
        $this->setUpPlayersKeys();
    }

    public function export()
    {
        $game = $players = $decks = $hands = $graves = [];
        if ($this->sessionType == self::IMPORT_TYPE_UPDATE) {
            $game = $this->data;
        }

        foreach ($this->players as $playerId => $player) {
            $players[] = $player->export();
            if ($this->sessionType == self::IMPORT_TYPE_NORMAL) {
                $decks[] = $this->decks[$playerId]->export();
                $hands[] = $this->hands[$playerId]->export();
                $graves[] = $this->graves[$playerId]->export();
            }
        }
        $game['players'] = $players;
        if ($decks) { $game['decks']   = $decks; }
        if ($hands) { $game['hands']   = $hands; }
        if ($graves) { $game['graves'] = $graves; }

        foreach (self::$exportValues as $valueName) {
            $game[$valueName] = $this->{$valueName};
        }
        if ($this->sessionType == self::IMPORT_TYPE_NORMAL) {
            $game['field'] = $this->field->export();
            $cards = [];
            foreach ($this->cards as $card) {
                $cards[$card->id] = $card->export();
            }
            $game['cards'] = $cards;
        }
        $game['log'] = $this->log->export();

        return $game;
    }

    public function render($playerId)
    {
        //var_dump($this->gameType); die;
        $data = [
            'card'     => $this->currentCardId,
            'js'       => [
                'phase'           => $this->phase,
                'card'            => $this->currentCardId,
                'playerTurnId'    => $this->playerTurnId,
                'turnNumber'      => $this->turnNumber,
                'subscriptionKey' => $this->getPlayerKey($this->currentPlayerId),
                'currentPlayerId' => $this->currentPlayerId,
                'actionUrl'       => $this->config['actionUrl'],
            ],
            'log' => $this->log->render(GameLog::RENDER_MODE_ADMIN)
        ];
        $data['hand']  = $this->renderHand($playerId);
        $data['field'] = $this->renderField($playerId);
        $data['opponentHand'] = $this->renderOpponentHand($playerId);

        if ($this->gameResult) {
            if (!empty($this->gameResult['draw'])) {
                $data['result'] = self::GAME_RESULT_DRAW;
            } else {
                if ($this->currentPlayerId == $this->gameResult['winner']) {
                    $data['result'] = self::GAME_RESULT_WIN;
                } else {
                    $data['result'] = self::GAME_RESULT_LOOSE;
                }
            }
        }
        // var_dump($this->log->getNextEventId());
        // var_dump($this->players[$this->currentPlayerId]->lastEventSeen); die;
        $this->players[$this->currentPlayerId]->lastEventSeen = $this->log->getNextEventId();
        return $data;
    }

    public function renderUpdate()
    {
        $data = [];
        $lastEvent = $this->players[$this->currentPlayerId]->lastEventSeen;
        $data['log'] = $this->log->renderUpdate($lastEvent);
        $data['game'] = $this->getGameUpdate();
        $this->players[$this->currentPlayerId]->lastEventSeen = $this->log->getNextEventId();
        return $data;
    }

    protected function getGameUpdate()
    {
        return [
            'turnNumber'   => $this->turnNumber,
            'playerTurnId' => $this->playerTurnId,
            'card'         => $this->currentCardId,
        ];
    }

    public function pushActions()
    {
        foreach ($this->log->newActions as $action) {
            $this->pushActionsPrepare($action);
        }
        $this->pushActionsSend();
        foreach ($this->players as $playerId => $value) {
            $this->players[$playerId]->lastEventSeen = $this->log->getNextEventId();
        }

    }

    public function action($name, $data = [])
    {
        if ($this->sessionType == Game::IMPORT_TYPE_UPDATE) {
            throw new \Exception("There is no way to do actions during ping!", 1);
        }
        if ($this->phase == self::PHASE_GAME_END) {
            return;
        }
        $this->log->logAction($name, $data, $this->playerTurnId);

        switch ($name) {
            case self::GAME_ACTION_DEPLOY:
                $this->deploy($data['cardId'], $data['x'], $data['y']);
                break;

            case self::GAME_ACTION_SKIP:
                $this->actionSkip($data['cardId']);
                break;

            case self::GAME_ACTION_MOVE:
                $this->actionMove($data['cardId'], $data['x'], $data['y']);
                break;

            case self::GAME_ACTION_CAST:
                $this->actionCast($data['cardId'], $data['data']);
                break;

            default:
                # code...
                break;
        }
        $this->gameAutoActions();
    }

    public function deploy($cardId, $x, $y, $isBotAction = false)
    {
        $card = $this->getCard($cardId);
        if (!$isBotAction && $this->currentPlayerId != $card->owner) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is trying to deploy not his card", 1);
        }
        if ($card->owner != $this->playerTurnId) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is trying to do deploy not on his turn", 1);
        }
        if ($y < Game::HEIGHT - 2) {
            throw new \Exception("This field is forbidden for deploy", 1);
        }

        list($x, $y) = $this->convertCoordinats($x, $y, $card->owner);

        if (!$this->field->isDeployable($x, $y)) {
            throw new \Exception("This field is forbidden for deploy", 1);
        }

        $card->unit->x = $x;
        $card->unit->y = $y;
        $this->moveCards([$card], self::LOCATION_HAND, self::LOCATION_FIELD);
        $card->unit->deploy();
        $this->players[$this->currentPlayerId]->skippedTurn = false;

        $this->nextTurn();
    }

    protected function actionMove($cardId, $x, $y, $isBotAction = false)
    {
        $card = $this->getCard($cardId);
        if (!$isBotAction && $this->currentPlayerId != $card->owner) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is trying to move not his unit", 1);
        }
        if ($card->owner != $this->playerTurnId) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is trying to move not on his turn", 1);
        }
        $leftSteps = $this->field->moveUnit($card, $x, $y);

        if (!$leftSteps) {
            $this->unitAttack();
        }
    }

    protected function actionSkip($cardId)
    {
        if ($this->currentPlayerId != $this->playerTurnId) {
            throw new \Exception('Player with id = ' . $this->currentPlayerId . ' is trying to Skip not on his turn');
        }
        if ($this->phase == self::PHASE_UNIT_DEPLOYING) {
            $this->players[$this->currentPlayerId]->skippedTurn = true;
            $this->nextTurn();
        } else if($this->phase == self::PHASE_BATTLE) {
            $this->unitAttack();
        }
    }

    protected function actionCast($cardId, $data)
    {
        $card = $this->getCard($cardId);
        if (!$this->hands[$this->playerTurnId]->hasCard($cardId) || $this->currentPlayerId != $card->owner) {
            throw new \Exception("Player with ID = " . $this->currentPlayerId . " is trying to use wrong spell", 1);
        }
        if ($this->spellsPlayed[$this->playerTurnId] >= Spell::MAX_SPELL_PER_TURN) {
            throw new Exception("Player with ID = " . $this->currentPlayerId . " is trying cast more spells in one turn then he can!", 1);
        }

        $card->spell->cast($data);

        $this->spellsPlayed[$this->playerTurnId] ++;

        $this->moveCards([$card], self::LOCATION_HAND, self::LOCATION_GRAVE);

        $this->unitAttack();
    }

    public function gameAutoActions()
    {
        switch ($this->phase) {
            case self::PHASE_GAME_NOT_STARTED:
                // the game is just created
                $handSize = $this->config['handDraw'];
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
                if ($this->isItBotTurn()) {
                    if ($this->hands[$this->playerTurnId]->count() > 0) {
                        $cardId = $this->hands[$this->playerTurnId]->getRandom();
                        list($x, $y) = $this->field->getRandomDeployCell($this->playerTurnId);
                        $this->deploy($cardId, $x, $y, true);
                    } else {
                        $this->nextTurn();
                    }
                }
                $playersFinished = 0;
                foreach ($this->players as $id => $player) {
                    if ($player->skippedTurn || $this->hands[$id]->count() == 0) {
                        $playersFinished++;
                    }
                }
                if ($playersFinished == count($this->players)) {
                    if ($this->field->isEachPlayerPlayedEnoughCards()) {
                        // GO to next phase!!
                        $this->startBattle();
                    }
                }
                break;
            case self::PHASE_BATTLE:
                $this->checkGameEnd();
                if ($this->currentCardId === null) {
                    $this->nextBattleCard();
                }
                if ($this->isItBotTurn()) {
                    $this->botBattleMove();
                }
                break;
        }
    }

    public function nextTurn()
    {
        switch ($this->phase) {
            case self::PHASE_UNIT_DEPLOYING:
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
                break;
            case self::PHASE_BATTLE:
                foreach ($this->field->cards as $cardId) {
                    $this->cards[$cardId]->unit->endOfTurn();
                }
                $this->turnNumber++;
                foreach ($this->spellsPlayed as &$value) {
                    $value = 0;
                }
                break;
        }
    }

    protected function startBattle()
    {
        $this->phase = self::PHASE_BATTLE;
        $this->log->logStartBattle();

        foreach ($this->players as $id => $player) {
            $this->drawCards($id, $this->config['spellsDraw']);
        }
        $this->turnNumber = 1;

        $this->gameAutoActions();
    }

    protected function botBattleMove()
    {
        $this->unitAttack();
        $this->gameAutoActions();
    }

    public function checkGameEnd()
    {
        $playersLost = [];
        foreach ($this->kings as $playerId => $kingId) {
            $king = $this->getCard($kingId);
            if ($king->location == Card::CARD_LOCATION_GRAVE) {
                $playersLost[] = $playerId;
            }
        }
        if ($playersLost) {
            $this->log->logBattleEnd();
            $result = [];
            if (count($playersLost) == 1) {
                $result['loser'] = $playersLost[0];
                foreach ($this->players as $id => $player) {
                    if ($id != $result['loser']) {
                        $result['winner'] = $id;
                        break;
                    }
                }
            } else {
                $result['draw'] = true;
            }
            $this->gameResult = $result;
            $this->phase = self::PHASE_GAME_END;
        }
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
            $this->cards[$cardId]->init();
            $this->log->logDraw($playerId, $cardId);
        }

        $this->moveCards($cards, self::LOCATION_DECK, self::LOCATION_HAND);
    }

    public function moveCards($cards, $from, $to)
    {
        foreach ($cards as $card) {
            if ($from == self::LOCATION_FIELD) {
                $this->field->removeUnit($card);
            } else {
                $this->{$from}[$card->owner]->remove([$card->id]);
            }
            if ($to == self::LOCATION_FIELD) {
                $this->field->addCard($card);
            } else {
                $this->{$to}[$card->owner]->addCards([$card]);
            }
            $card->location = Card::$locations[$to];
        }
    }

    protected function renderField($playerId)
    {
        $data = $this->field->render($playerId, $this->phase == self::PHASE_BATTLE);
        $data['map'] = [];
        foreach ($data['cards'] as $cardData) {
            $card = $this->cards[$cardData];
            $renderedCard = $card->render($playerId);
            $data['map'][] = $renderedCard;

        }
        unset($data['cards']);

        return $data;
    }

    protected function renderHand($playerId)
    {
        $data = [];
        $cards = $this->hands[$playerId]->cards;
        foreach ($cards as $cardId) {
            $data[] = $this->cards[$cardId]->render($playerId);
        }
        return $data;
    }

    protected function renderOpponentHand($playerId)
    {
        foreach ($this->players as $id => $player) {
            if ($playerId != $id) {
                // here our enemy
                return ['size' => $this->hands[$id]->count(), 'playerId' => $id];
            }
        }
    }

    protected function unitAttack()
    {
        $this->cards[$this->currentCardId]->unit->attack();
        $this->nextBattleCard();
    }

    protected function nextBattleCard()
    {
        $this->currentCardId = $this->field->getNextCard($this->currentCardId);
        if ($this->currentCardId === null) {

            $this->nextTurn();
            $this->nextBattleCard();

        } else {
            if ($this->cards[$this->currentCardId]->owner != $this->playerTurnId) {
                $this->playerTurnId = $this->cards[$this->currentCardId]->owner;
            }
        }
    }
}