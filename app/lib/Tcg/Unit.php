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
    const KEYWORD_SHIELD      = 'shield';

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
        'data',
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

    public $data;

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

        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_UNIT_DEPLOY, $this->card->id);
        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_UNIT_DEPLOY_TO_CELL, $this->x . '_' . $this->y, ['cardId' => $this->card->id]);
    }

    public function render($playerId)
    {
        $data      = [
            'config' => $this->config,
            'attack' => $this->attack,
        ];
        list ($x, $y) = $this->card->game->field->convertCoordinats($this->x, $this->y, $playerId);
        //list($x, $y) = [$this->x, $this->y];
//        $data['x'] = empty($extData['x']) ? $this->x : $extData['x'];
//        $data['y'] = empty($extData['y']) ? $this->y : $extData['y'];
        $data['x'] = $x;
        $data['y'] = $y;
        $data['keywords'] = $this->keywords;

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
        
        $targets = $this->getTargets();

        if (!$targets) {
            $this->attackNoTargets();
            return;
        }
        // we have possible targets
        $target = $this->choseTarget($targets);
        $damage = $this->getDamage($target);

        $this->beforeAttack($damage, $target);

        $damage = $target->unit->applyDamage($damage, $this->card);

        $this->afterAttack($damage, $target);

        $this->card->game->log->logAttack($this->name, $this->card->owner, $target->unit->name, $damage);
    }

    protected function getTargets()
    {
        $range = self::DEFAULT_ATTACK_RANGE;
        if (isset($this->config['attackRange'])) {
            $range = $this->config['attackRange'];
        }
        $enemies = $this->card->game->getAllPlayerEnemies($this->card->owner);
        return $this->card->game->field->getAllPlayersUnitsInRange($this->x, $this->y, $range, $enemies);
    }

    /**
     * @param Card[] $targets
     *
     * @return Card
     */
    protected function choseTarget($targets)
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
        if ($shield = $this->getShield()) {
            $damage -= $shield;
            if ($damage < 0) return 0;
        }
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
    public function healDamage($damage, Card $sourceCard) {
        $this->currentHealth += $damage;
        if ($this->currentHealth > $this->maxHealth) {
            $this->currentHealth = $this->maxHealth;
        }
    }

    public function death()
    {
        $this->card->game->triggerEvent(Game::EVENT_UNIT_DEATH, ['target' => $this]);
        $this->card->game->moveCards([$this->card], Game::LOCATION_FIELD, GAME::LOCATION_GRAVE);
    }

    public function move($x, $y)
    {
        $distance = $this->card->game->field->getDistance($this->x, $this->y, $x, $y) + $this->stepsMade;
        if (empty($this->config['moveDistance'])) {
            $moveDistance = self::DEFAULT_MOVE_DISTANCE;
        } else {
            $moveDistance = $this->config['moveDistance'];
        }
        if ($moveDistance < $distance) {
            throw new \Exception('Unit cant move that far distance is = ' . $distance);
        }
        $this->beforeMove();
        $this->x = $x;
        $this->y = $y;
        $this->stepsMade += 1;
        return $moveDistance - $this->stepsMade;
    }

    public function endOfTurn()
    {
        $this->stepsMade = 0;
    }

    public function hasKeyword($word) {
        return in_array($word, $this->keywords);
    }
    public function addKeyword($word, $data = null) {
        if (!$this->hasKeyword($word)) {
            $this->keywords[] = $word;
        }
        switch ($word) {
            case self::KEYWORD_SHIELD:
                $this->data[self::KEYWORD_SHIELD] = $data;
                break;
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

    public function getShield()
    {
        if ($this->hasKeyword(self::KEYWORD_SHIELD)) {
            if (!isset($this->data[self::KEYWORD_SHIELD])) {
                throw new \Exception('Unit have shield, but value is missing');
            }
            return $this->data[self::KEYWORD_SHIELD];
        }
        return false;
    }

    protected function beforeAttack($damage, Card $target) {

    }
    protected function afterAttack($damage, Card $target) {

    }
    protected function beforeMove() {
        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_BEFORE_UNIT_MOVE, $this->card->id);
        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_UNIT_MOVE_FROM_CELL, $this->x . '_' . $this->y, ['cardId' => $this->card->id]);
    }
    public function afterMove() {
        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_UNIT_MOVE_TO_CELL, $this->x . '_' . $this->y, ['cardId' => $this->card->id]);
        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_AFTER_UNIT_MOVE, $this->card->id);
    }
    protected function attackNoTargets() {

    }
    
}