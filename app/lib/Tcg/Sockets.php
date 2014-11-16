<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg;


trait Sockets {

    protected $socket;
    public $playerKeys = [];
    public $preparedActions = [];

    public function setUpPlayersKeys()
    {
        foreach ($this->players as $playerId => $player) {
            $this->playerKeys[$playerId] = 'g_' . $this->gameId . '_p' . $playerId;
        }
    }

    public function initSockets()
    {
        $context = new \ZMQContext();
        $this->socket = $context->getSocket(\ZMQ::SOCKET_PUSH, 'my pusher');
        $this->socket->connect("tcp://localhost:5555");


    }

    public function getPlayerKey($playerId)
    {
        return $this->playerKeys[$playerId];
    }

    public function getTeamKeys($teamId)
    {
        $team = $this->teams[$teamId];
        $result = [];
        foreach ($team as $playerId) {
            $result[] = $this->getPlayerKey($playerId);
        }
        return $result;
    }

    public function pushActionsPrepare($action)
    {
        $type = $action[0];
        $data = $action[1];
        $targets = [];
        $event = [
            'type' => $type
        ];
        $toPush = [];
        switch ($type) {
            case GameLog::LOG_TYPE_DEPLOY:
                $targets = $this->getAllKeys();
                $card    = $this->getCard($data[1]);
                foreach ($targets as $playerId => $key) {
                    $event = [
                        'type' => $type,
                        'playerId' => $data[0],
                        'card'     => $card->render($playerId)
                    ];
                    $toPush[] = [$playerId, $event];
                }
                break;
            case GameLog::LOG_TYPE_START_BATTLE:
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_CARD_DRAW:
                $targets = $this->getAllKeys();
                foreach ($targets as $playerId => $key) {
                    $event = [
                        'type' => $type
                    ];
                    if ($data[0] == $playerId) {
                        $event['card'] = $this->getCard($data[1])->render($data[0]);
                    } else {
                        $event['card'] = true;
                    }
                    $event['playerId'] = $data[0];
                    $toPush[] = [$playerId, $event];
                }
                break;
            case GameLog::LOG_TYPE_MOVE:
                $event['cardId'] = $data[0];
                $targets = $this->getAllKeys();
                foreach ($targets as $playerId => $key) {
                    list($event['x'], $event['y']) = $this->convertCoordinats(
                        $data[1],
                        $data[2],
                        $playerId
                    );
                    $toPush[] = [$playerId, $event];
                }
                break;
            case GameLog::LOG_TYPE_UNIT_GET_DAMAGE:
                $event['cardId'] = $data[0];
                $event['health'] = $data[1];
                $event['damage'] = $data[2];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_ATTACK:
                $event['cardId'] = $data[0];
                $event['targetId'] = $data[1];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_UNIT_CHANGE_ARMOR:
                $event['cardId'] = $data[0];
                $event['armor']  = $data[1];
                $event['dArmor'] = $data[2];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_UNIT_DEATH:
                $event['cardId'] = $data[0];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_CAST:
                $event['cardId'] = $data[0];
                $event['spell']  = $data[1];
                $event['data']   = $data[2];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_UNIT_CHANGE:
                $event['cardId']   = $data[0];
                $event['dataType'] = $data[1];
                $event['data']     = $data[2];
                $toPush[] = ['all', $event];
                break;
            case GameLog::LOG_TYPE_UNIT_SKIP:
                $event['cardId'] = $data[0];
                $toPush[] = ['all', $event];
                break;
            default:
                return;
        }

        foreach ($toPush as $pushData) {
            $target = $pushData[0];
            $event = $pushData[1];
            if ($target === 'all') {
                foreach ($this->playerKeys as $playerId => $key) {
                    $this->addPreparedAction($playerId, $event);
                }
            } else {
                $this->addPreparedAction($target, $event);
            }
        }
    }

    protected function addPreparedAction($key, $event)
    {
        if (!isset($this->preparedActions[$key])) {
            $this->preparedActions[$key] = [];
        }
        $this->preparedActions[$key][] = $event;
    }

    public function pushActionsSend()
    {
        $game = $this->getGameUpdate();
        foreach ($this->preparedActions as $playerId => $events) {
            $data = [
                'key' => $this->getPlayerKey($playerId),
                'log' => $events,
                'game' => $game
            ];
            $this->socket->send(json_encode($data));
        }

    }

    public function getAllKeys()
    {
        return $this->playerKeys;
    }
} 