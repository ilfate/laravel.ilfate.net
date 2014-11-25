<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class GameBuilder {

    public static function buildTest($currentPlayerId, $config)
    {

        $player1 = new Player($currentPlayerId, 1);
        $player2 = new Player(2, 2);
        if (!empty($config['isBot'])) {
            $player2->type = Player::PLAYER_TYPE_BOT;
        }

        $game      = new Game($currentPlayerId);
        $game->log = new GameLog($game);
        $game->gameType = Game::GAME_TYPE_TEST;
        if (!empty($config['debug'])) {
            $game->gameType = Game::GAME_TYPE_DEBUG;
        }
        $game->setUpGameObject();
        $game->sessionType = Game::IMPORT_TYPE_NORMAL;

        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->createLocations();

        $configs = \Config::get('tcg.cards');
        $deck1 = [
            [Card::createFromConfig($configs[1], $game), 2],
            [Card::createFromConfig($configs[2], $game), 2],
            [Card::createFromConfig($configs[3], $game), 2],
            [Card::createFromConfig($configs[4], $game), 2],
            [Card::createFromConfig($configs[5], $game), 2],
            [Card::createFromConfig($configs[6], $game), 2],
            [Card::createFromConfig($configs[7], $game), 1],
        ];
        $deck2 = [
            [Card::createFromConfig($configs[51], $game), 2],
            [Card::createFromConfig($configs[52], $game), 2],
            [Card::createFromConfig($configs[53], $game), 2],
            // [Card::createFromConfig($configs[54], $game), 2],
            [Card::createFromConfig($configs[55], $game), 2],
            [Card::createFromConfig($configs[56], $game), 2],
            [Card::createFromConfig($configs[57], $game), 2],
            [Card::createFromConfig($configs[59], $game), 1],
        ];
        foreach ($deck2 as $card) {
            for($i = 0; $i < $card[1]; $i++) {
                $game->setUpCard(clone $card[0], $player1->id);
            }
        }
        foreach ($deck1 as $card) {
            for($i = 0; $i < $card[1]; $i++) {
                $game->setUpCard(clone $card[0], $player2->id);
            }
        }
        $game->init();
        $game->start();
        $game->gameAutoActions();
        return $game;
    }

    public static function buildSituation($currentPlayerId, $situation, $config)
    {
        $situation = [
            'cards' => [
                [
                    'id'    => 1,
                    'owner' => 1,
                    'x' => 1,
                    'y' => 1,
                    'currentHealth' => 1,
                    'isKing' => true,
                    'armor' => 12,
                    'maxHealth' => 99,
                    'keywords' => ['focus'],
                    'isCurrent' => true,
                ],
                [
                    'id'    => 52,
                    'owner' => 2,
                    'x' => 3,
                    'y' => 3,
                    'isKing' => true,
                    'currentHealth' => 20,
                    'keywords' => ['focus'],
                    'maxHealth' => 99
                ],
            ],
            'playerTurnId' => 1,
        ];

        $game      = new Game($currentPlayerId);
        $game->log = new GameLog($game);
        $game->sessionType = Game::IMPORT_TYPE_NORMAL;
        $game->gameType = Game::GAME_TYPE_TEST;
        $game->setUpGameObject();

        $player1 = new Player($currentPlayerId, 1);
        $player2 = new Player(2, 2);
        if (!empty($config['isBot'])) {
            $player2->type = Player::PLAYER_TYPE_BOT;
        }

        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->createLocations();
        $game->playerTurnId = $situation['playerTurnId'];

        $configs = \Config::get('tcg.cards');
        foreach ($situation['cards'] as $cardData) {
            $cardConfig = $configs[$cardData['id']];
            if (!empty($cardData['isKing'])) {
                $cardConfig['isKing'] = true;
                unset($cardData['isKing']);
            }
            $card = Card::createFromConfig($cardConfig, $game);
            $card->init();
            $card->unit->deploy();
            $game->setUpCard($card, $cardData['owner']);
            $x = $cardData['x'];
            $y = $cardData['y'];
            list($x, $y) = $game->convertCoordinats($x, $y, $card->owner);
            //var_dump($card); die;
            $card->unit->x = $x;
            $card->unit->y = $y;
            $game->moveCards([$card], Game::LOCATION_HAND, Game::LOCATION_FIELD);
            $keys = ['currentHealth', 'armor', 'maxArmor' , 'keywords', 'attack', 'maxHealth', 'moveSteps', 'moveType'];
            foreach ($keys as $keyName) {
                if (isset($cardData[$keyName])) {
                    $card->unit->{$keyName} = $cardData[$keyName];
                }
            }
            if (isset($cardData['isCurrent'])) {
                $game->currentCardId = $card->id;
            }
        }
        $game->init();
        $game->phase = Game::PHASE_BATTLE;
        $game->start();
        $game->gameAutoActions();
        return $game;
    }
}