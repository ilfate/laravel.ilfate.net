<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Player {

    const PLAYER_TYPE_PLAYER = 'player';
    const PLAYER_TYPE_BOT    = 'bot';

    public $id;

    public $name = 'default name';

    public $team;

    public $skippedTurn = false;

    /**
     * player || bot
     * @var string
     */
    public $type = 'player';

    public function __construct($id, $team)
    {
        $this->id   = $id;
        $this->team = $team;
    }

    public static function import($data)
    {
        $player = new Player($data['id'], $data['team']);
        $player->type = $data['type'];
        return $player;
    }

    public function export()
    {
        $data = [
            'id'   => $this->id,
            'type' => $this->type,
            'team' => $this->team,
        ];

        return $data;
    }
}