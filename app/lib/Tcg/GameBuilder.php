<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class GameBuilder {

	public static function build($currentPlayerId, $config)
	{

		$player1 = new Player($currentPlayerId, 1);
        $player2 = new Player(2, 2);
        if (!empty($config['isBot'])) {
            $player2->type = Player::PLAYER_TYPE_BOT;
        }

        $game      = new Game($currentPlayerId);
        $game->log = new GameLog($game);

        if (!empty($config['debug'])) {
        	$game->gameType = 'debug';
        }
        $game->sessionType = Game::IMPORT_TYPE_NORMAL;

        $game->addPlayer($player1);
        $game->addPlayer($player2);
        $game->createLocations();

        $configs = \Config::get('tcg.cards');
        $deck1 = [
            Card::createFromConfig($configs[0], $game),
            Card::createFromConfig($configs[1], $game),
            Card::createFromConfig($configs[2], $game),
            Card::createFromConfig($configs[3], $game),
            Card::createFromConfig($configs[4], $game),
            Card::createFromConfig($configs[5], $game),
            
        ];
        $deck2 = [
            Card::createFromConfig($configs[51], $game),
            Card::createFromConfig($configs[52], $game),
            Card::createFromConfig($configs[53], $game),
            Card::createFromConfig($configs[54], $game),
            Card::createFromConfig($configs[55], $game),
            Card::createFromConfig($configs[56], $game),
            Card::createFromConfig($configs[57], $game),
        ];
        foreach ($deck2 as $card) {
            $game->setUpCard(clone $card, $player1->id);
            $game->setUpCard(clone $card, $player1->id);
        }
        foreach ($deck1 as $card) {
            $game->setUpCard(clone $card, $player2->id);
            $game->setUpCard(clone $card, $player2->id);
        }
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
                    'x' => 3,
                    'y' => 2,
                    'currentHealth' => 10,
                    'armor' => 12,
                    'maxHealth' => 99,
                    'keywords' => ['focus'],
                    'isCurrent' => true,
                ],
                [
                    'id'    => 1,
                    'owner' => 2,
                    'x' => 3,
                    'y' => 1,
                    'currentHealth' => 10,
                    'keywords' => ['focus'],
                    'maxHealth' => 99
                ],
            ],
            'playerTurnId' => 1,
        ];

        $game      = new Game($currentPlayerId);
        $game->log = new GameLog($game);
        $game->sessionType = Game::IMPORT_TYPE_NORMAL;

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
            $card = Card::createFromConfig($configs[$cardData['id']], $game);
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
            $keys = ['currentHealth', 'armor', 'maxArmor' , 'keywords', 'attack', 'maxHealth'];
            foreach ($keys as $keyName) {
                if (isset($cardData[$keyName])) {
                    $card->unit->{$keyName} = $cardData[$keyName];
                }
            }
            if (isset($cardData['isCurrent'])) {
                $game->currentCardId = $card->id;
            }
        }
        $game->phase = Game::PHASE_BATTLE;
        $game->start();
        $game->gameAutoActions();
        return $game;
    }
}