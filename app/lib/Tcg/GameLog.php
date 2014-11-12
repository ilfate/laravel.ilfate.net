<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class GameLog
{

    const LOG_TYPE_TEXT              = 'text';
    const LOG_TYPE_PLAYER_ACTION     = 'playerAction';
    const LOG_TYPE_DEPLOY            = 'deploy';
    const LOG_TYPE_CARD_DRAW         = 'cardDraw';
    const LOG_TYPE_START_BATTLE      = 'startBattle';
    const LOG_TYPE_MOVE              = 'move';
    const LOG_TYPE_UNIT_GET_DAMAGE   = 'unitGetDamage';
    const LOG_TYPE_UNIT_CHANGE_ARMOR = 'unitChangeArmor';
    const LOG_TYPE_ATTACK            = 'attack';
    const LOG_TYPE_UNIT_DEATH        = 'death';
    const LOG_TYPE_CAST              = 'cast';
    const LOG_TYPE_UNIT_CHANGE       = 'change';

    const RENDER_MODE_PUBLIC = 'public';
    const RENDER_MODE_ADMIN  = 'admin';

    protected static $publicLogs = [
        self::LOG_TYPE_TEXT,
        self::LOG_TYPE_DEPLOY,
        self::LOG_TYPE_START_BATTLE,
        self::LOG_TYPE_MOVE,
        self::LOG_TYPE_CARD_DRAW,
        self::LOG_TYPE_UNIT_GET_DAMAGE,
        self::LOG_TYPE_UNIT_CHANGE_ARMOR,
        self::LOG_TYPE_UNIT_DEATH,
        self::LOG_TYPE_CAST,
        self::LOG_TYPE_UNIT_CHANGE,
        self::LOG_TYPE_ATTACK,
    ];

    /**
     * @var Game
     */
    public $game;

    /**
     *
     *
     *
     * @var array
     */
    protected $log = [];

    public function __construct(Game $game)
    {
        $this->game = $game;
    }

    /**
     * @return GameLog
     */
    public static function import($data, Game $game)
    {
        $log      = new GameLog($game);
        $log->log = $data['log'];
        return $log;
    }

    public function export()
    {
        $data = [
            'log' => $this->log
        ];

        return $data;
    }

    public function render($mode = self::RENDER_MODE_PUBLIC)
    {
        if ($mode == self::RENDER_MODE_PUBLIC) {
            $available = self::$publicLogs;
        }
        $data = [];
        for ($i = count($this->log) - 1; $i >= 0; $i--) {
            if (!isset($available) || in_array($this->log[$i][0], $available)) {
                $data[] = $this->renderMessage($this->log[$i][0], $this->log[$i][1]);
            }
        }
        return $data;
    }

    public function logText($text)
    {
        $this->log[] = [
            self::LOG_TYPE_TEXT,
            [
                $text
            ]
        ];
    }

    public function logAction($actionName, $data, $playerId)
    {
        $this->log[] = [
            self::LOG_TYPE_PLAYER_ACTION,
            [
                $actionName,
                $data,
                $playerId
            ]
        ];
    }

    public function logDeploy($playerId, $cardId)
    {
        $this->log[] = [
            self::LOG_TYPE_DEPLOY,
            [
                $playerId,
                $cardId
            ]
        ];
    }

    public function logStartBattle()
    {
        $this->log[] = [
            self::LOG_TYPE_START_BATTLE,
            []
        ];
    }

    public function logDraw($playerId, $cardId)
    {
        $this->log[] = [
            self::LOG_TYPE_CARD_DRAW,
            [
                $playerId,
                $cardId
            ]
        ];
    }

    public function logMove($cardId, $x, $y)
    {
        $this->log[] = [
            self::LOG_TYPE_MOVE,
            [
                $cardId,
                $x,
                $y
            ]
        ];
    }

    public function logUnitGetDamage($cardId, $health, $damage)
    {
        $this->log[] = [
            self::LOG_TYPE_UNIT_GET_DAMAGE,
            [
                $cardId,
                $health,
                $damage
            ]
        ];
    }

    public function logUnitChangeArmor($cardId, $armor, $dArmor)
    {
        $this->log[] = [
            self::LOG_TYPE_UNIT_CHANGE_ARMOR,
            [
                $cardId,
                $armor,
                $dArmor
            ]
        ];
    }

    public function logDeath($cardId)
    {
        $this->log[] = [
            self::LOG_TYPE_UNIT_DEATH,
            [
                $cardId
            ]
        ];
    }

    public function logCast($cardId, $spell, $data)
    {
        $this->log[] = [
            self::LOG_TYPE_CAST,
            [
                $cardId,
                $spell,
                $data
            ]
        ];
    }

    public function logUnitChange($cardId, $dataType, $data)
    {
        $this->log[] = [
            self::LOG_TYPE_UNIT_CHANGE,
            [
                $cardId,
                $dataType,
                $data
            ]
        ];
    }

    public function logAttack($cardId, $targetId)
    {
        $this->log[] = [
            self::LOG_TYPE_ATTACK,
            [
                $cardId,
                $targetId
            ]
        ];
    }

    protected function renderMessage($type, $data)
    {
        return '';
        switch ($type) {
            case self::LOG_TYPE_TEXT:
                return $data[0];
                break;
            case self::LOG_TYPE_PLAYER_ACTION:
                $text = $this->getPLayerInfo($data[2]);
                $text .= ' performed action "' . $data[0] . '" with data: ' . json_encode($data[1]);
                return $text;
                break;
            case self::LOG_TYPE_DEPLOY:
                $text = $this->getPLayerInfo($data[0]);
                $text .= ' deployed unit with id = ' . $data[1];
                return $text;
                break;
            case self::LOG_TYPE_MOVE:
                $text = $this->getPLayerInfo($data[1]);
                $text .= ' moved "' . $data[0] . '"';
                return $text;
                break;
            case self::LOG_TYPE_ATTACK:
                $text = $this->getPLayerInfo($data[1]);
                $text .= ' attacket with "' . $data[0] . '" and dealt ' . $data[3] . ' damage to "' . $data[2] . '"';
                return $text;
                break;

            default:
                # code...
                break;
        }
    }

    public function renderUpdate($lastEvent)
    {
        $render = [];
        $till   = count($this->log);
        for ($i = $lastEvent; $i < $till; $i++) {
            $log = $this->log[$i];
            if (!in_array($log[0], self::$publicLogs)) {
                continue;
            }
            $event = [
                'type' => $log[0],
            ];
            switch ($log[0]) {
                case self::LOG_TYPE_DEPLOY:
                    $card              = $this->game->cards[$log[1][1]];
                    $event['playerId'] = $log[1][0];
                    $event['card']     = $card->render($this->game->currentPlayerId);
                    break;
                case self::LOG_TYPE_START_BATTLE:
                    break;
                case self::LOG_TYPE_CARD_DRAW:
                    if ($this->game->currentPlayerId == $log[1][0]) {
                        $event['card'] = $this->game->cards[$log[1][1]]->render($log[1][0]);
                    } else {
                        $event['card'] = true;
                    }
                    $event['playerId'] = $log[1][0];
                    break;
                case self::LOG_TYPE_MOVE:
                    $event['cardId'] = $log[1][0];
                    list($event['x'], $event['y']) = $this->game->field->convertCoordinats(
                        $log[1][1],
                        $log[1][2],
                        $this->game->currentPlayerId
                    );
                    break;
                case self::LOG_TYPE_UNIT_GET_DAMAGE:
                    $event['cardId'] = $log[1][0];
                    $event['health'] = $log[1][1];
                    $event['damage'] = $log[1][2];
                    break;
                case self::LOG_TYPE_ATTACK:
                    $event['cardId'] = $log[1][0];
                    $event['targetId'] = $log[1][1];
                    break;
                case self::LOG_TYPE_UNIT_CHANGE_ARMOR:
                    $event['cardId'] = $log[1][0];
                    $event['armor']  = $log[1][1];
                    $event['dArmor'] = $log[1][2];
                    break;
                case self::LOG_TYPE_UNIT_DEATH:
                    $event['cardId'] = $log[1][0];
                    break;
                case self::LOG_TYPE_CAST:
                    $event['cardId'] = $log[1][0];
                    $event['spell']  = $log[1][1];
                    $event['data']   = $log[1][2];
                    break;
                case self::LOG_TYPE_UNIT_CHANGE:
                    $event['cardId']   = $log[1][0];
                    $event['dataType'] = $log[1][1];
                    $event['data']     = $log[1][2];
                    break;
                default :
                    throw new \Exception('Not implemented log update render');

            }
            $render[] = $event;
        }
        return $render;
    }

    public function getNextEventId()
    {
        return count($this->log);
    }

    protected function getPLayerInfo($playerId)
    {
        $player = $this->game->players[$playerId];
        $text   = $player->name . '(' . $player->id . ') ';
        return $text;
    }
}

