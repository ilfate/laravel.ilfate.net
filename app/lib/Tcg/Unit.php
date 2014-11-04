<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

use ClassPreloader\Config;

class Unit
{

    const RENDER_TYPE_UNIT = 'unit';
    const RENDER_TYPE_CARD = 'card';

    const CONFIG_VALUE_TOTAL_HEALTH = 'totalHealth';
    const CONFIG_VALUE_TEXT         = 'text';
    const CONFIG_VALUE_ATTACK       = 'attack';
    const CONFIG_VALUE_ARMOR        = 'armor';

    const DEFAULT_MOVE_DISTANCE = 1;
    const DEFAULT_ATTACK_RANGE  = 1;

    /**
     * totalHealth
     * text
     *
     * @var array
     */
    public $config;

    public $maxHealth;
    public $currentHealth;
    public $maxArmor = 0;
    public $armor = 0;
    public $x;
    public $y;

    /**
     * @var Card
     */
    public $card;

    public $effects = array();
    public $lastMoveTurn;

    /**
     * @var Effect\Effect[]
     */
    protected $effectObjects = array();

    public static function createFromConfig($config, Card $card)
    {
        $unit         = new $config['unit']();
        $unit->config = $config;
        $unit->card   = $card;

        return $unit;
    }

    public static function import($data, $unitId, $card)
    {
        $unit = Unit::createFromConfig(\Config::get('tcg.units.' . $unitId), $card);

        $unit->currentHealth = $data['currentHealth'];
        $unit->maxHealth     = $data['maxHealth'];
        $unit->armor         = $data['armor'];
        $unit->maxArmor      = $data['maxArmor'];
        $unit->effects       = $data['effects'];
        $unit->lastMoveTurn  = $data['lastMoveTurn'];
        $unit->x             = $data['x'];
        $unit->y             = $data['y'];
        $unit->initEffects();
        return $unit;
    }

    public function export()
    {
        $this->updateEffects();
        $data = [
            'currentHealth' => $this->currentHealth,
            'maxHealth'     => $this->maxHealth,
            'armor'         => $this->armor,
            'maxArmor'      => $this->maxArmor,
            'effects'       => $this->effects,
            'lastMoveTurn'  => $this->lastMoveTurn,
            'x'             => $this->x,
            'y'             => $this->y,
        ];
        return $data;
    }

    public function deploy()
    {
        $this->currentHealth = $this->config[self::CONFIG_VALUE_TOTAL_HEALTH];
        $this->maxHealth     = $this->config[self::CONFIG_VALUE_TOTAL_HEALTH];

        if (!empty($this->config[self::CONFIG_VALUE_ARMOR])) {
            $this->armor    = $this->config[self::CONFIG_VALUE_ARMOR];
            $this->maxArmor = $this->config[self::CONFIG_VALUE_ARMOR];
        }
    }

    public function render($extData)
    {
        $data      = [
            'config' => $this->config,
        ];
        $data['x'] = empty($extData['x']) ? $this->x : $extData['x'];
        $data['y'] = empty($extData['y']) ? $this->y : $extData['y'];
        if ($this->card->location == Card::CARD_LOCATION_FIELD) {
            $data['currentHealth'] = $this->currentHealth;
            $data['maxHealth']     = $this->maxHealth;
            if ($this->armor) {
                $data['armor'] = $this->armor;
            }
        }
        return $data;
    }

    public function attack()
    {
        if ($this->card->location != Card::CARD_LOCATION_FIELD) {
            throw new \Exception('Unit is trying to attack, but he is not on the Field!!');
        }
        $range = self::DEFAULT_ATTACK_RANGE;
        if (isset($this->config['attackRange'])) {
            $range = $this->config['attackRange'];
        }
        $targets = $this->card->game->field->getAllPossibleAttackTargets($this->x, $this->y, $range, $this->card->owner);

        if (!$targets) {
            return;
        }
        // we have possible targets
        $target = $this->choseTarget($targets);
        $damage = $this->getDamage();
        $target->unit->applyDamage($damage, $this->card);
    }

    /**
     * @param $targets
     *
     * @return Card
     */
    public function choseTarget($targets)
    {
        return $targets[array_rand($targets)];
    }

    protected function getDamage()
    {
        $attack = $this->config['attack'];
        return rand($attack[0], $attack[1]);
    }

    public function applyDamage($damage, Card $sourceCard)
    {
        if ($this->armor) {
            if ($damage > $this->armor) {
                $damage -= $this->armor;
                $this->armor = 0;
            } else {
                $this->armor -= $damage;
                $damage = 0;
            }
        }
        if ($damage > 0) {
            $this->card->game->triggerEvent(Game::EVENT_UNIT_GET_DAMAGE, ['target' => $this]);
        }
        $this->currentHealth -= $damage;
        if ($this->currentHealth <= 0) {
            $this->death();
        }
    }

    public function death()
    {
        $this->card->game->moveCards([$this->card], Game::LOCATION_FIELD, GAME::LOCATION_GRAVE);
    }

    public function move($x, $y)
    {
        //if ($this->lastMoveTurn !=)
        $distance = $this->card->game->field->getDistance($this->x, $this->y, $x, $y);
        if (empty($this->config['moveDistance'])) {
            $moveDistance = self::DEFAULT_MOVE_DISTANCE;
        } else {
            $moveDistance = $this->config['moveDistance'];
        }
        if ($moveDistance < $distance) {
            throw new \Exception('Unit cant move that far distance is = ' . $distance);
        }
        $this->x = $x;
        $this->y = $y;
    }

    protected function initEffects()
    {
        foreach ($this->effects as $effect) {
            $this->effectObjects[] = new $effect[0]($effect[1]);
        }
    }

    protected function updateEffects()
    {
        $this->effects = array();
        foreach ($this->effectObjects as $effect) {
            $this->effects[] = [get_class($effect), $effect->export()];
        }
    }
}