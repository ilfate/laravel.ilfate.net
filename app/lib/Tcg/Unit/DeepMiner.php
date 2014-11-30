<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use \Tcg\Unit;
use \Tcg\Card;

class DeepMiner extends Unit {

    public function death()
    {
        $this->card->game->drawCards($this->card->owner, 1);

        parent::death();
    }
}