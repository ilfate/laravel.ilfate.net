<?php
/**
 * ilfate.net
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */

namespace Vortex;

class Vortex {
	protected $map;

	public function __construct() 
	{
		
	}

	public static function createFromArray($data)
	{
		$vortex = new Vortex();
	}

	protected function setData($data)
	{
		$this->map = $data['map'];
	}
}