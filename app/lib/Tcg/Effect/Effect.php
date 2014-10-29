<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Tcg\Effect;

abstract class Effect
{
    protected $prams = array();


    public function __construct($params)
    {
        $this->params = $params;
    }

    public function export()
    {
        return $this->params;
    }
}