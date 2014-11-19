<?php
/**
 * PHPulsar
 * @autor Ilya Rubinchik ilfate@gmail.com
 * 2014
 */


namespace Tcg;


trait UnitMove {

	protected function checkMoveToCell($currentX, $currentY, $x, $y, $moveType)
	{
		$possibleMoves = $this->getCellsForMoveType($currentX, $currentY, $moveType);
		foreach ($possibleMoves as $move) {
			if ($move[0] == $x && $move[1] == $y) {
				return true;
			}
		}
		return false;
	}

	protected function getCellsForMoveType($x, $y, $moveType)
	{
		switch ($moveType) {
			case Unit::MOVE_TYPE_NORMAL:
				return [
					[$x + 1, $y],
					[$x - 1, $y],
					[$x, $y + 1],
					[$x, $y - 1],
				];

			case Unit::MOVE_TYPE_DIAGONAL:
				return [
					[$x + 1, $y + 1],
					[$x - 1, $y - 1],
					[$x - 1, $y + 1],
					[$x + 1, $y - 1],
				];

			case Unit::MOVE_TYPE_AROUND:
				return [
					[$x + 1, $y],
					[$x - 1, $y],
					[$x, $y + 1],
					[$x, $y - 1],
					[$x + 1, $y + 1],
					[$x - 1, $y - 1],
					[$x - 1, $y + 1],
					[$x + 1, $y - 1],
				];
			case Unit::MOVE_TYPE_JUMP:
				return [
					[$x - 1, $y - 2],
					[$x - 2, $y - 1],
					[$x + 1, $y + 2],
					[$x + 2, $y + 1],
					[$x + 1, $y - 2],
					[$x + 2, $y - 1],
					[$x - 1, $y + 2],
					[$x - 2, $y + 1],
				];

		}
	}

}
