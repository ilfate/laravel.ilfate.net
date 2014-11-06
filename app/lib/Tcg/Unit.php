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
    const CONFIG_VALUE_KEYWORDS     = 'keywords';
    const CONFIG_VALUE_NAME         = 'name';

    const DEFAULT_MOVE_DISTANCE = 1;
    const DEFAULT_ATTACK_RANGE  = 1;

    const KEYWORD_BLOODTHIRST = 'bloodthirst';
    const KEYWORD_FOCUS       = 'focus';

    protected static $exportValues = array(
        'maxHealth',
        'currentHealth',
        'maxArmor',
        'armor',
        'x',
        'y',
        'stepsMade',
        'effects',
        'keywords',
        'attack',
    );

    /**
     * totalHealth
     * text
     *
     * @var array
     */
    public $config;
    public $name;

    public $maxHealth;
    public $currentHealth;
    public $maxArmor = 0;
    public $armor = 0;
    public $attack = [0, 0];
    public $x;
    public $y;

    /**
     * @var Card
     */
    public $card;

    public $effects   = [];
    public $keywords  = [];
    public $stepsMade = 0;

    /**
     * @var Effect\Effect[]
     */
    protected $effectObjects = array();

    public static function createFromConfig($config, Card $card)
    {
        $unit         = new $config['unit']();
        $unit->config = $config;
        $unit->card   = $card;
        $unit->name   = $config[self::CONFIG_VALUE_NAME];

        return $unit;
    }

    public static function import($data, $unitId, $card)
    {
        $unit = Unit::createFromConfig(\Config::get('tcg.units.' . $unitId), $card);

        foreach (self::$exportValues as $valueName) {
            $unit->{$valueName} = $data[$valueName];
        }

        $unit->initEffects();
        return $unit;
    }

    public function export()
    {
        $this->updateEffects();
        $data = [];
        foreach (self::$exportValues as $valueName) {
            $data[$valueName] = $this->{$valueName};
        }
        return $data;
    }

    public function deploy()
    {
        $this->currentHealth = $this->config[self::CONFIG_VALUE_TOTAL_HEALTH];
        $this->maxHealth     = $this->config[self::CONFIG_VALUE_TOTAL_HEALTH];
        $this->attack        = $this->config[self::CONFIG_VALUE_ATTACK];

        if (!empty($this->config[self::CONFIG_VALUE_ARMOR])) {
            $this->armor    = $this->config[self::CONFIG_VALUE_ARMOR];
            $this->maxArmor = $this->config[self::CONFIG_VALUE_ARMOR];
        }
        if (!empty($this->config[self::CONFIG_VALUE_KEYWORDS])) {
            $this->keywords = $this->config[self::CONFIG_VALUE_KEYWORDS];
        }

        $this->card->game->triggerEvent(Game::EVENT_UNIT_DEPLOY, ['target' => $this]);
    }

    public function render($extData)
    {
        $data      = [
            'config' => $this->config,
            'attack' => $this->attack,
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
        $damage = $this->getDamage($target);
        $damage = $target->unit->applyDamage($damage, $this->card);

        $this->card->game->log->logAttack($this->name, $this->card->owner, $target->unit->name, $damage);
    }

    /**
     * @param Card[] $targets
     *
     * @return Card
     */
    public function choseTarget($targets)
    {
        if ($bloodthirstTaget = $this->isBloodthirst($targets)) {
            return $bloodthirstTaget;
        }
        $targets = $this->isFocusTargets($targets);
        return $targets[array_rand($targets)];
    }

    protected function isBloodthirst($targets)
    {
        if ($this->hasKeyword(self::KEYWORD_BLOODTHIRST)) {
            $theMostInjured = null;
            $theBiggestDamage = 0;
            foreach ($targets as $card) {
                $injure = $card->unit->maxHealth - $card->unit->currentHealth;
                if ($injure && $injure > $theBiggestDamage) {
                    $theMostInjured   = $card;
                    $theBiggestDamage = $injure;
                }
            }
            if ($theMostInjured) {
                return $theMostInjured;
            }
        }
        return false;
    }

    protected function isFocusTargets($targets)
    {
        $focusOnly = array_filter($targets, function ($card) {
            return $card->unit->hasKeyword(self::KEYWORD_FOCUS);
        });
        if ($focusOnly) {
            return $focusOnly;
        }
        return $targets;
    }

    protected function getDamage(Card $target)
    {
        if ($this->stepsMade > 0) {
            $damage = $this->attack[0];
        } else {
            $damage = rand($this->attack[0], $this->attack[1]);
        }
        $this->card->game->triggerEvent(Game::EVENT_UNIT_DEAL_DAMAGE, ['target' => $this, 'damage' => &$damage]);
        return $damage;
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
            $this->card->game->triggerEvent(Game::EVENT_UNIT_GET_DAMAGE, ['target' => $this, 'damage' => &$damage]);
        }
        $this->currentHealth -= $damage;
        if ($this->currentHealth <= 0) {
            $this->death();
        }
        return $damage;
    }

    public function death()
    {
        $this->card->game->triggerEvent(Game::EVENT_UNIT_DEATH, ['target' => $this]);
        $this->card->game->moveCards([$this->card], Game::LOCATION_FIELD, GAME::LOCATION_GRAVE);
    }

    public function move($x, $y)
    {
        $distance = $this->card->game->field->getDistance($this->x, $this->y, $x, $y);
        if (empty($this->config['moveDistance'])) {
            $moveDistance = self::DEFAULT_MOVE_DISTANCE;
        } else {
            $moveDistance = $this->config['moveDistance'];
        }
        if ($moveDistance < $distance) {
            throw new \Exception('Unit cant move that far distance is = ' . $distance);
        }
        $this->card->game->triggerEvent(Game::EVENT_UNIT_MOVE, ['target' => $this]);
        $this->x = $x;
        $this->y = $y;
        $this->stepsMade += 1;
    }

    public function endOfTurn()
    {
        $this->stepsMade = 0;
    }

    public function hasKeyword($word) {
        return in_array($word, $this->keywords);
    }
    public function addKeyword($word) {
        if (!$this->hasKeyword($word)) {
            $this->keywords[] = $word;
        }
    }
    public function removeKeyword($word)
    {
        if ($this->hasKeyword($word)) {
            $key = array_search($word, $this->keywords);
            unset($this->keywords[$key]);
        }
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