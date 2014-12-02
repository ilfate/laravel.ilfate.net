<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Unit;

use ClassPreloader\Config;
use Tcg\Game;
use \Tcg\Unit;
use \Tcg\Card;

class AxeThrower extends UnitCanThrowAxe {

    public function deploy()
    {
        parent::deploy();
        $this->setAttack([6, 6]);
        $this->set('attackRange', 2);
        $this->data['axe'] = true;
    }
}