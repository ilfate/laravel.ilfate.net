<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

abstract class Spell {

    const CONFIG_VALUE_TEXT   = 'text';
    const CONFIG_VALUE_TYPE   = 'type';
    const CONFIG_VALUE_NAME   = 'name';

    const SPELL_TYPE_UNIT = 'unit';
    const SPELL_TYPE_CELL = 'cell';
    const SPELL_TYPE_CAST = 'cast';

    const MAX_SPELL_PER_TURN = 1;

    /**
     * @var array
     */
    public $config;

    public $name;

    /**
     * @var Card
     */
    public $card;

    public static function createFromConfig($config, $card)
    {
        $spell = new $config['spell']();
        $spell->config = $config;
        $spell->card = $card;
        $spell->name = $config[self::CONFIG_VALUE_NAME];

        return $spell;
    }

    public static function import($data, $spellId, $card)
    {
        $spell = Spell::createFromConfig(\Config::get('tcg.spells.' . $spellId), $card);
        return $spell;
    }

    public function export()
    {
        $data = [

        ];
        return $data;
    }

    public function render()
    {
        $data = [
            'config' => $this->config
        ];

        return $data;
    }

    /**
     *
     * $data[
     * 'targetId' => $cardId,
     * 'x' => $x,
     * 'y' => $y
     * ]
     */
    public function cast($data)
    {
        switch($this->config['type']) {
            case self::SPELL_TYPE_UNIT:
                if (!isset($data['targetId'])) {
                    throw new \Exception(__CLASS__ . " spell used without target", 1);
                }
                $target = $this->card->game->getCard($data['targetId']);
                if ($target->location != Card::CARD_LOCATION_FIELD) {
                    throw new \Exception(__CLASS__ . " spell used on card that is not in field", 1);
                }
                $this->castUnit($target);
                break;
        }
    }

    abstract public function castUnit(Card $target);

    public function logCast($data = array())
    {
        $this->card->game->log->logCast($this->card->id, $this->config['name'], $data);
    }

}