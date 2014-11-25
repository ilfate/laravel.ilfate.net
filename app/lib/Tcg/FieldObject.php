<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg;

use ClassPreloader\Config;

class FieldObject
{
    protected static $exportValues = array(
        'x',
        'y',
        'id',
        'objectId',
    );

    public $id;
    public $objectId;

    public $x;
    public $y;

    /**
     * @var Field
     */
    public $field;
    public $config;

    public static function createFromConfig($objectId, Field $field)
    {
        $config = \Config::get('tcg.fieldObjects.' . $objectId);
        $object         = new $config['class']();
        $object->config = $config;
        $object->field  = $field;
        $object->objectId = $objectId;

        return $object;
    }

    public static function import($data, Field $field)
    {
        $unit = FieldObject::createFromConfig($data['objectId'], $field);

        foreach (self::$exportValues as $valueName) {
            $unit->{$valueName} = $data[$valueName];
        }

        return $unit;
    }

    public function export()
    {
        $data = [];
        foreach (self::$exportValues as $valueName) {
            $data[$valueName] = $this->{$valueName};
        }
        return $data;
    }

    public function deploy()
    {

    }

    public function render($playerId)
    {
        $data      = [
            'config' => $this->config,
        ];
        list ($x, $y) = $this->field->game->convertCoordinats($this->x, $this->y, $playerId);

        $data['x'] = $x;
        $data['y'] = $y;
        return $data;
    }



    public function death()
    {
//        $this->card->game->moveCards([$this->card], Game::LOCATION_FIELD, GAME::LOCATION_GRAVE);
//        $this->card->game->log->logDeath($this->card->id);
//        $this->card->game->triggerEvent(Game::EVENT_TRIGGER_UNIT_DEATH, $this->card->id);
    }
    
}