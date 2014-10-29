<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

class Player {

    public $id;

    public $name;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public static function import($data)
    {
        $player = new Player($data['id']);
        return $player;
    }

    public function export()
    {
        $data = [
            'id'   => $this->id,
        ];

        return $data;
    }
}