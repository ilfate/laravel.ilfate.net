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
    ME.Game.markEmptyDirectCells(unit.x, unit.y);
    ME.Game.addUnit(unit);
});


ME.Game = function () {
	this.fieldConfig = [];
	this.cellWidth = 64;
	this.fieldSize = 11;
    this.center = 5;
    this.powerLimit = 10;
    this.spawns = [
//        [this.center, 0],
//        [0, this.center],
//        [this.center, this.fieldSize - 1],
//        [this.fieldSize - 1, this.center],
        [0, 2],
        [2, 0],
        [this.fieldSize - 1, this.fieldSize - 1 - 2],
        [this.fieldSize - 1 - 2, this.fieldSize - 1],
        [0, this.fieldSize - 1 - 2],
        [this.fieldSize - 1 - 2, 0],
        [this.fieldSize - 1, 2],
        [2, this.fieldSize - 1],
    ];
    this.turnPoints = {
        '2_3' : true,
        '3_2' : true
    };
    this.sideRoads = {
        '2_0' : true,
        '2_1' : true,
        '2_2' : true,
        '0_2' : true,
        '1_2' : true,
        '2_3' : true,
        '3_2' : true,
        '3_3' : true,
        '3_4' : true,
        '4_3' : true,
    };


    this.isClickable =true;
    this.isGameRunning = true;

	this.units = [];
	this.unitIndex = [];
	this.enemies = [];
	this.enemyIndex = [];

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
        this.drawRoads();
	}

    this.drawRoads = function () {
        for(var i = 0; i < this.fieldSize; i++) {
            for(var i2 = 0; i2 < this.fieldSize; i2++) {
                draw = false;
                if (
                    (i == this.center || i2 == this.center)
                    || this.sideRoads[i2 + '_' + i] !== undefined
                    || this.sideRoads[(this.fieldSize - 1 - i2) + '_' + (this.fieldSize - 1 - i)] !== undefined
                    || this.sideRoads[(this.fieldSize - 1 - i2) + '_' + (i)] !== undefined
                    || this.sideRoads[(i2) + '_' + (this.fieldSize - 1 - i)] !== undefined
                    ) {
                    el = $('<div class="road-brick"></div>').css({
                        'top': ((i + 0.5) * this.cellWidth) -3 + 'px',
                        'left': ((i2 + 0.5) * this.cellWidth) -3 + 'px'
                    });
                    $('.me2 .roads').append(el);
                }
            }
        }
    }

	this.fieldClick = function(cell) {
        if (!this.isClickable || !this.isGameRunning) {
            return false;
        }
        this.isClickable = false;
		var x = cell.data('x');
		var y = cell.data('y');

		//if empty
		var inCoordinats = this.getByCoordinats(x, y);
		if (inCoordinats) {
			return this.stopClick();
		}
		
		//if have direct neibour
		if (!this.isDirectNeibour(x, y)) {
			return this.stopClick();
		}

		var isUnitCreated = this.createUnit(x, y);
		if (!isUnitCreated) {
			return this.stopClick();
		}
        $('.cell.actionPossible').removeClass('actionPossible');
        var thus = this;
        setTimeout(function() {thus.turn()}, 500);
	}

    this.stopClick = function() {
        this.isClickable = true;
        return false;
    }

	this.turn = function() {
        var thus = this;
        this.moveAttackers();
        this.tryToSpawnEnemy();
		this.animateReproductions();

        setTimeout(function() {thus.unitsGrow()}, 500);
        setTimeout(function() {thus.stopClick()}, 1000);

	}

    this.tryToSpawnEnemy = function() {
        if (rand(1,3) == 3) {
            var enemyCoords = this.spawns[rand(0, this.spawns.length - 1)];
            var enemyPower = rand(1, 5);

            var enemy = new ME.Enemy(ME.Game, enemyPower, enemyCoords[0], enemyCoords[1]);
            enemy.render();
            ME.Game.addEnemy(enemy);
        }
    }

	this.moveAttackers = function() {
        for (var i in this.enemies) {
            var x = 0; var y =0;
            var enemy = this.enemies[i];
            switch (enemy.direction) {
                case 0: x = enemy.x; y = enemy.y - 1; break;
                case 1: x = enemy.x + 1; y = enemy.y; break;
                case 2: x = enemy.x; y = enemy.y + 1; break;
                case 3: x = enemy.x - 1; y = enemy.y; break;
            }

            var onCell = this.getByCoordinats(x, y);
            if (onCell) {
                this.battle(enemy, onCell);
            } else {
                enemy.animateMove(x, y);
                if (x == this.center && y == this.center) {
                    this.isGameRunning = false;
                }
            }
        }
	}

	this.battle = function(enemy, unit) {
        if (unit.isEnemy !== undefined) {
            return false;
        }
        if (unit.isReproductive()) {
            enemy.animateEat(unit.x, unit.y);
            unit.eaten();
        } else {
            if (enemy.power > unit.power) {
                unit.destroyed();
                enemy.damaged(unit.power);
            } else if (enemy.power < unit.power) {
                unit.damaged(enemy.power);
                enemy.destroyed();
            } else {
                enemy.destroyed();
                unit.destroyed();
            }
        }
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
//                if (unit.power >= this.powerLimit) {
//                    unit.explode();
//                }
                if (unit.isReproductive()) {
                    this.markEmptyDirectCells(unit.x, unit.y);
                }
			}
		}
	}

	this.createUnit = function(x, y) {
		var neibours = this.getAllNeibours(x, y);
		var newUnitPower = 0;
        var warriors = [];
		for (var i in neibours) {
			if (neibours[i].isReproductive()) {
				newUnitPower += neibours[i].power / 2;
				neibours[i].reproduction(x, y);
			} else {
                warriors.push(neibours[i])
            }
		}
		if (newUnitPower == 0) {
			return false;
		}
        for (var i in warriors) {
            if (!warriors[i].isReproductive() && warriors[i].power > 1) {
                var d = Math.floor(warriors[i].power / 2);
                newUnitPower += d;
                warriors[i].reproduction(x, y, d);
            }
        }

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
    this.addEnemy = function(enemy) {
        this.enemies.push(enemy);
        var id = this.enemies.length - 1;
        this.enemyIndex[enemy.x + '_' + enemy.y] = id;
    }
	this.getByCoordinats = function(x, y) {
		var id = this.getIdByCoordinats(x, y);
        if (id !== false && this.units[id] !== undefined) {
            return this.units[id];
        }
        var enemyId = this.getEnemyIdByCoordinats(x, y);
        if (enemyId !== false && this.enemies[enemyId] !== undefined) {
            return this.enemies[enemyId];
        }
		return false;
	}
    this.getIdByCoordinats = function(x, y) {
        if (this.unitIndex[x + '_' + y] !== undefined) {
            return this.unitIndex[x + '_' + y];
        }
        return false;
    }
    this.getEnemyIdByCoordinats = function(x, y) {
        if (this.enemyIndex[x + '_' + y] !== undefined) {
            return this.enemyIndex[x + '_' + y];
        }
        return false;
    }
    this.markEmptyDirectCells = function(x, y) {
        var neibourCellsCoordinats = this.getDirectNeibourCoords(x, y);
        for (var i in neibourCellsCoordinats) {
            var neibour = this.getByCoordinats(neibourCellsCoordinats[i][0], neibourCellsCoordinats[i][1])
            if (!neibour) {
                $('.cell.x-' + neibourCellsCoordinats[i][0] + '.y-' + neibourCellsCoordinats[i][1]).addClass('actionPossible')
            }
        }
    }
	this.isDirectNeibour = function(x, y) {
		var neiboursCoordinats = this.getDirectNeibourCoords(x, y);
		var neibours = [];
		for (var i in neiboursCoordinats) {
			var neibour = this.getByCoordinats(neiboursCoordinats[i][0], neiboursCoordinats[i][1])
			if (neibour && neibour.isUnit == true && neibour.isReproductive()) {
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
			if (neibour && neibour.isUnit) {
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
    this.isUnit = true;
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
		this.obj = $('<div></div>').addClass('unit').css({
			'top': (this.game.cellWidth * this.y ) + 'px',
			'left': (this.game.cellWidth * this.x ) + 'px',
			'width' : this.game.cellWidth + 'px',
			'height' : this.game.cellWidth + 'px',
			'line-height' : this.game.cellWidth + 'px'
		});
		$('.me2 .units').append(this.obj);
        this.setState();

        this.animateGrow();


	}
	this.animateGrow = function() {
		this.isAnimateGrow = false;
        var objState = this.obj.data('state');
        if (objState != this.state) {
		    if (this.isReproductive()) {
                this.obj.animate({
                    'border-radius': '50' + 'px'
                });
            } else {
                this.obj.animate({
                    'border-radius': 0
                });
            }
        }
        this.obj.data('state', this.state);
        this.obj.html(this.power);
	}

	this.setState = function() {
		if (this.power % 2 == 1) {
			this.state = 'warrior';
		} else {
			this.state = 'reproduction';
		}
	}

	this.reproduction = function(x, y, d) {
        if (!d) {
            d = this.power / 2;
        }
		this.setDeltaPower(-d);
        this.animateGrow();
		this.isAnimateReproduction = true;
	}

    this.explode = function() {
        this.isAnimateExplode = true;
        this.obj.remove();
        this.remove();
    }

    this.eaten = function() {
        info('unit eaten');
        this.obj.animate({
            'width' : (this.game.cellWidth * 2) + 'px',
            'height' : (this.game.cellWidth * 2) + 'px',
            'opacity' : 0
        }, {
            'duration' : 500,
            'complete' : function() {
                $(this).remove();
            }
        });
        this.obj.remove();
        this.remove();
    }

    this.remove = function() {
        var id = this.game.getIdByCoordinats(this.x, this.y);
        delete(this.game.units[id]);
        delete(this.game.unitIndex[this.x + '_' + this.y]);
    }

	this.setDeltaPower = function(d) {
		this.power += d;
		this.setState();
	}

	this.isReproductive = function() {
		return this.state == 'reproduction'
	}
    this.damaged = function(damage) {
        this.power -= damage;
        this.obj.html(this.power);
    }
    this.destroyed = function() {
        info('unit destroyed');
        this.remove();
        this.obj.animate({
            'opacity' : 0
        }, {
            'duration' : 500,
            'complete' : function() {
                $(this).remove();
            }
        });
    }

}

ME.Enemy = function(game, power, x ,y) {
    this.isEnemy = true;
    this.game = game;
    this.power = power;
    this.x = x;
    this.y = y;         //  0
    this.direction = 0; // 3 1
                        //  2

    this.oldX = 0;
    this.oldY = 0;

    this.obj = {};

    this.render = function() {

        if (this.y == 0) {
            this.direction = 2;
        } else if (this.y == this.game.fieldSize - 1) {
            this.direction = 0;
        } else if (this.x == this.game.fieldSize - 1) {
            this.direction = 3;
        } else if (this.x == 0) {
            this.direction = 1;
        }

        this.obj = $('<div></div>').addClass('enemy').css({
            'top': (this.game.cellWidth * this.y ) + 'px',
            'left': (this.game.cellWidth * this.x ) + 'px',
            'width' : this.game.cellWidth + 'px',
            'height' : this.game.cellWidth + 'px',
            'line-height' : this.game.cellWidth + 'px'
        }).html(this.power);
        $('.me2 .enemies').append(this.obj);
    }

    this.animateMove = function(x, y) {
        this.move(x, y);
        this.obj.animate({
            'top': (this.game.cellWidth * this.y ) + 'px',
            'left': (this.game.cellWidth * this.x ) + 'px'
        }, 400)
    }

    this.animateEat = function(x, y) {
        this.move(x, y);
        this.obj.animate({
            'top': (this.game.cellWidth * this.y ) + 'px',
            'left': (this.game.cellWidth * this.x ) + 'px'
        }, 400)
    }

    this.move = function(x, y) {
        this.oldX = this.x;
        this.oldY = this.y;
        this.x = x;
        this.y = y;
        var id = this.game.getEnemyIdByCoordinats(this.oldX, this.oldY);
        delete(this.game.enemyIndex[this.oldX + '_' + this.oldY]);
        this.game.enemyIndex[this.x + '_' + this.y] = id;
        if (
            (this.x == this.game.center && this.direction != 0 && this.direction != 2)
            || (this.y == this.game.center && this.direction != 1 && this.direction != 3)) {
            if (this.x == this.game.center) {
                if (this.y > this.game.center) { this.direction = 0; }
                else { this.direction = 2; }
            } else {
                if (this.x > this.game.center) { this.direction = 3; }
                else { this.direction = 1; }
            }
        } else if (this.game.turnPoints[this.x + '_' + this.y] !== undefined
            || this.game.turnPoints[(this.game.fieldSize - 1 - this.x) + '_' + (this.game.fieldSize - 1 - this.y)] !== undefined
            || this.game.turnPoints[(this.game.fieldSize - 1 - this.x) + '_' + this.y] !== undefined
        || this.game.turnPoints[(this.x) + '_' + (this.game.fieldSize - 1 - this.y)] !== undefined) {
            switch (this.direction) {
                case 0:
                case 2:
                    if (this.x > this.game.center) { this.direction = 3; }
                    else { this.direction = 1; }
                    break;
                case 3:
                case 1:
                    if (this.y > this.game.center) { this.direction = 0; }
                    else { this.direction = 2; }
                    break;
            }
        }
    }

    this.destroyed = function() {
        var id = this.game.getEnemyIdByCoordinats(this.x, this.y);
        delete(this.game.enemies[id]);
        delete(this.game.enemyIndex[this.x + '_' + this.y]);
        this.obj.animate({
            'opacity' : 0
        }, {
            'duration' : 500,
            'complete' : function() {
                $(this).remove();
            }
        })
    }

    this.damaged = function(damage) {
        this.power -= damage;
        this.obj.html(this.power);
    }


}