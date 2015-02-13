/**
 * Created by Ilya Rubinchik (ilfate) on 13/02/15.
 */

function rand(min, max)
{
    return Math.floor(Math.random()*(max-min+1)+min);
}
function info(data)
{
    console.info(data);
}
function debug(data) {
    //info(data);
    // desabled
}
function isInt(n){
    return typeof n== "number" && isFinite(n) && n%1===0;
}
function is_object(obj) {
    return typeof obj === 'object';
}

function ME () {

}
ME = new ME();

$(document).ready(function() {
    ME.Game = new ME.Game();
    ME.Game.drawField();

    var unit = new ME.Unit(ME.Game, 2);
    unit.x = 5;
    unit.y = 5;
    unit.isSkipGrow = false;
    unit.render();
    ME.Game.addUnit(unit);
});


ME.Game = function () {
	this.fieldConfig = [];
	this.cellWidth = 64;
	this.fieldSize = 11;

	this.units = [];
	this.unitIndex = [];

	this.drawField = function() {
		var html = '';
		for(var i = 0; i < this.fieldSize; i++) {
			for(var i2 = 0; i2 < this.fieldSize; i2++) {
				html += '<div class="cell x-' + i2 + ' y-' + i + '" data-x="' + i2 + '" data-y=' + i + '></div>';
			}
		}
		$('.me2 .field').css({
			'width': (this.fieldSize * this.cellWidth) + 'px'
		});
		$('.me2 .field').append(html);
		$('.me2 .field .cell').css({
			'width': this.cellWidth + 'px',
			'height': this.cellWidth + 'px'
		}).on({
			'click': function(){ ME.Game.fieldClick($(this))}
		})
	}

	this.fieldClick = function(cell) {
		var x = cell.data('x');
		var y = cell.data('y');

		//if empty
		var inCoordinats = this.getByCoordinats(x, y);
		if (inCoordinats) {
			return false;
		}
		
		//if have direct neibour
		if (!this.isDirectNeibour(x, y)) {
			return false;
		}

		var isUnitCreated = this.createUnit(x, y);
		if (!isUnitCreated) {
			return false;
		}
		this.turn();
	}

	this.turn = function() {

		this.animateReproductions();
		this.moveAttackers();
		this.battles();
		this.unitsGrow();
	}

	this.moveAttackers = function() {

	}

	this.battles = function() {

	}

	this.unitsGrow = function() {
		for (var i in this.units) {
			var unit = this.units[i];
			if (unit) {
				if (unit.isSkipGrow) {
					unit.isSkipGrow = false;
				} else {
					unit.setDeltaPower(1);
					unit.isAnimateGrow = true;
					unit.animateGrow();
				}
			}
		}
	}

	this.createUnit = function(x, y) {
		var neibours = this.getAllNeibours(x, y);
		var newUnitPower = 0;
		for (var i in neibours) {
			info(neibours[i]);
			if (neibours[i].isReproductive()) {
				newUnitPower += neibours[i].power / 2;
				neibours[i].reproduction(x, y);
			}
		}
		if (newUnitPower == 0) {
			return false;
		}
		info('newPower = ' + newUnitPower);

		var unit = new ME.Unit(ME.Game, newUnitPower);
	    unit.x = x;
	    unit.y = y;
	    unit.render();
	    ME.Game.addUnit(unit);
	    return true;
	}

	this.animateReproductions = function() {

	}	

	this.addUnit = function(unit) {
		this.units.push(unit);
		var id = this.units.length - 1;
		this.unitIndex[unit.x + '_' + unit.y] = id;
	}
	this.getByCoordinats = function(x, y) {
		if (this.unitIndex[x + '_' + y] !== undefined) {
			var id = this.unitIndex[x + '_' + y];
			if (this.units[id] !== undefined) {
				return this.units[id];
			}
		}
		return false;
	}
	this.isDirectNeibour = function(x, y) {
		var neiboursCoordinats = this.getDirectNeibourCoords(x, y);
		var neibours = [];
		for (var i in neiboursCoordinats) {
			var neibour = this.getByCoordinats(neiboursCoordinats[i][0], neiboursCoordinats[i][1])
			if (neibour && neibour.isReproductive()) {
				neibours.push(neibour);
			}
		}
		if (neibours.length > 0) {
			return true;
		}
		return false;
	}
	this.getAllNeibours = function(x, y) {
		var neiboursCoordinats = this.getNeibourCoords(x, y);
		var neibours = [];
		for (var i in neiboursCoordinats) {
			var neibour = this.getByCoordinats(neiboursCoordinats[i][0], neiboursCoordinats[i][1])
			if (neibour) {
				neibours.push(neibour);
			}
		}
		if (neibours.length > 0) {
			return neibours;
		}
		return false; 
	}

	this.getDirectNeibourCoords =function(x, y) {
		return [
			[x + 1, y],
			[x, y + 1],
			[x - 1, y],
			[x, y - 1],
		];
	}
	this.getNeibourCoords =function(x, y) {
		return [
			[x + 1, y + 1],
			[x - 1, y + 1],
			[x + 1, y - 1],
			[x - 1, y - 1],
			[x + 1, y],
			[x, y + 1],
			[x - 1, y],
			[x, y - 1],
			
		];
	}

}


ME.Unit = function (game, power) {
	this.isNew = true;
	this.game = game;
	this.power = power;
	this.x = 0;
	this.y = 0;

	// warrior | reproduction
	this.state = 'warrior';

	this.isSkipGrow = true;

	this.isAnimateReproduction = false;
	this.isAnimateGrow = false;
	this.isAnimateExplode = false;
	this.isAnimateDeath = false;

	this.obj = {};

	this.render = function() {
		this.obj = $('<div></div').addClass('unit').css({
			'top': (this.game.cellWidth * this.y ) + 'px',
			'left': (this.game.cellWidth * this.x ) + 'px',
			'width' : this.game.cellWidth + 'px',
			'height' : this.game.cellWidth + 'px',
			'line-height' : this.game.cellWidth + 'px'
		}).html(this.power);
		$('.me2 .units').append(this.obj);
		this.setState();
	}
	this.animateGrow = function() {
		this.isAnimateGrow = false;
		this.obj.html(this.power);
	}

	this.setState = function() {
		if (this.power % 2 == 1) {
			this.state = 'warrior';
		} else {
			this.state = 'reproduction';
		}
	}

	this.reproduction = function(x, y) {
		this.setDeltaPower(-(this.power / 2));
		this.isAnimateReproduction = true;
	}

	this.setDeltaPower = function(d) {
		this.power += d;
		this.setState();
	}

	this.isReproductive = function() {
		return this.state == 'reproduction'
	}

}