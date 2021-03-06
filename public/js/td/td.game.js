/**
 * Created by Ilya Rubinchik (ilfate) on 12/09/14.
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

function TD () {

}
TD = new TD();

$(document).ready(function() {

    $('#modalHowUnitMoveButton').bind('click', function(){
        //var src = 'http://www.youtube.com/v/OlJ9VdY9dig&amp;autoplay=1';
        $("#modalHowUnitMove").modal({                    // wire up the actual modal functionality and show the dialog
                    "backdrop"  : "static",
                    "keyboard"  : true,
                    "show"      : true                     // ensure the modal is shown immediately
        });
        var theModal = '#modalHowUnitMove',
        videoSRC = $('#modalHowUnitMove iframe').attr( "data-video" ), 
        videoSRCauto = videoSRC+"?autoplay=1" ;
        $(theModal+' iframe').attr('src', videoSRCauto);
        $(theModal+' .youtube-stop').click(function () {
            $(theModal+' iframe').attr('src', videoSRC);
        });
        // if (!$('#modalHowUnitMove iframe').attr('src')) {
        //     $('#modalHowUnitMove iframe').attr('src', src);
        // }
    });

    // $('#modalHowUnitMove .youtube-stop').click(function () {
    //     $('#modalHowUnitMove iframe').removeAttr('src');
    // });

    var situation = false;
//       {'units' : [
//
//           {'x': 2, 'y': 3, 'd': 0, 'a':true, 'p': 3, 'o':'bot'},
//           {'x': 1, 'y': 6, 'd': 0, 'a':true, 'p': 5, 'o':'bot'},
//           {'x': 5, 'y': 1, 'd': 0, 'a':true, 'p': 2, 'o':'bot'},
//           {'x': 8, 'y': 6, 'd': 3, 'a':true, 'p': 1, 'o':'bot'},
//           {'x': 6, 'y': 5, 'd': 3, 'a':true, 'p': 17, 'o':'bot', 'b' : true},
//           {'x': 6, 'y': 2, 'd': 3, 'a':true, 'p': 25, 'o':'bot', 'b' : true},
//           {'x': 2, 'y': 2, 'd': 2, 'a':true, 'p': 4, 'o':'player'},
//           {'x': 3, 'y': 3, 'd': 3, 'a':true, 'p': 35, 'o':'player'},
//           {'x': 7, 'y': 1, 'd': 2, 'a':true, 'p': 8, 'o':'player'},
//           {'x': 6, 'y': 4, 'd': 1, 'a':true, 'p': 18, 'o':'player'},
//           {'x': 4, 'y': 4, 'd': 2, 'a':false, 'p': 1, 'o':'player'},
//           {'x': 4, 'y': 0, 'd': 2, 'a':false, 'p': 24, 'o':'player'},
//           {'x': 0, 'y': 4, 'd': 2, 'a':false, 'p': 28, 'o':'player'},
//           {'x': 3, 'y': 8, 'd': 2, 'a':false, 'p': 2, 'o':'player'},
//           {'x': 7, 'y': 8, 'd': 2, 'a':false, 'p': 6, 'o':'player'},
//           {'x': 4, 'y': 5, 'd': 1, 'a':true, 'p': 12, 'o':'player'},
//           {'x': 4, 'y': 7, 'd': 2, 'a':true, 'p': 79, 'o':'player'}
//      ],
//       'bonuses': [
//           {'x': 1, 'y': 2, 'p': 5, 't':'plus'},
//           {'x': 7, 'y': 2, 'p': 8, 't':'plus'},
//           {'x': 6, 'y': 1, 'p': 3, 't':'minus'},
//           {'x': 2, 'y': 6, 'p': 4, 't':'minus'},
//       ]};
    var game = new TD.Game(situation);
    game.init();
});

TD.Game = function (situation) {
    this.facet      = new TD.Facet(this);
    this.mapConfig  = {};
    this.currentMap = {};
    this.newMap     = {};
    this.running    = true;
    this.units      = {};
    this.lastUnitId = 1;

    this.statsKilledPower   = 0;
    this.statsKilledUnits   = 0;
    this.statsLostUnits     = 0;
    this.statsLostPower     = 0;
    this.statsTicksSurvived = 0;
    this.statsPoints        = 0;

    this.pointsPerKill   = 5;

    this.spawnBotsEveryTick = 1;
    this.turnsBotWasSpawnd  = 0;

    this.chanceToSpawnBonus = 15;

    this.init = function() {
        this.mapConfig = new TD.Map.Config();
        this.mapConfig.setSize(9);
        this.mapConfig.setSpawn();

        this.currentMap = new TD.Map(this.facet, this.mapConfig);

        TD.Facet = this.facet;

        if (!situation) {

            this.spawnPlayerUnit();
            this.spawnBotUnit();
        } else {
            // situation emulation
            for(var key in situation.units) {
                var unitData = situation.units[key];
                var unit = new TD.Unit(this);
                unit.setPosition(unitData.x, unitData.y);
                unit.setOwner(unitData.o);
                unit.power = unitData.p;
                unit.isBoss = !!unitData.b
                unit.active = unitData.a;
                unit.direction = unitData.d;
                unit.init();
            }
            if (situation.bonuses) {
                for(var key in situation.bonuses) {
                    var bonusData = situation.bonuses[key];
                    var bonus = new TD.Bonus(this);
                    bonus.x = bonusData.x;
                    bonus.y = bonusData.y;
                    bonus.power = bonusData.p;
                    bonus.type = bonusData.t;
                    this.currentMap.putBonusToMap(bonus, bonus.x, bonus.y);
                }
            }
        }
        this.currentMap.drawMap();
        this.currentMap.draw(this.units);
    }

    this.getNewUnitId = function () {
        return this.lastUnitId++;
    }

    this.setUnit = function(unit) {
        debug('new unit:' + unit.getId() + '. Owner='+unit.owner);
        this.units[unit.getId()] = unit;
        this.currentMap.setUnit(unit);
    }

    this.removeUnit = function (unit) {
        debug ('remove unit id = ' + unit.getId());
        this.newMap.animateDeath(unit);
        delete this.units[unit.getId()];
    }

    this.getCenter = function() {
        return this.currentMap.getCenter();
    }

    this.checkUnitDirection = function(unit) {
        this.currentMap.checkUnitDirection(unit);
    }

    this.spawnPlayerUnit = function () {
        if (!this.running) {
            debug ('can`t spawn unit. Game is stopped');
            return;
        }
        var center = this.currentMap.getCenter();
        var unitIdInCenter = this.currentMap.get(center.x, center.y);
        if (!unitIdInCenter || this.units[unitIdInCenter] == undefined) {
            // spawn only if center is empty.
            debug ('SPAWN ' + unitIdInCenter);
            debug (this.units);
            var unit = new TD.Unit(this);
            unit.setPosition(center.x, center.y);
            unit.setOwner('player');
            unit.init();
        }
    }

    this.spawnBotUnit = function () {
        if (!this.running) {
            debug ('can`t spawn unit. Game is stopped');
            return;
        }
        if (this.turnsBotWasSpawnd == 0) {
            this.turnsBotWasSpawnd = this.spawnBotsEveryTick;
        } else {
            this.turnsBotWasSpawnd--;
            return;
        }
        var emptyCell = false;
        for (var i = 0; i < 5; i++) {
            //we will do 3 attempts to find empty cell.
            var cell = this.currentMap.getRandomBotSpawnCell();
            if (!this.currentMap.get(cell.x, cell.y)) {
                debug('random coordinats x = ' + cell.x + ' y = ' + cell.y);
                emptyCell = true;
                break;
            }
        }
        if (!emptyCell) {
            // we failed to find empty cell
            return;
        }
        var unit = new TD.Unit(this);
        unit.setPosition(cell.x, cell.y);
        unit.setOwner('bot');
        unit.activate();
        unit.init();
        this.tryToSpawnBoss(unit);
        this.currentMap.botUnitDirectionSetup(unit);
    }

    this.tryToSpawnBoss = function(unit) {
        var valueIncreasingWithTime = Math.round(this.statsTicksSurvived / 10);
        var valueIncreasingWithTimeSlow = Math.round(this.statsTicksSurvived / 15);
        var timeTillSpawnBoss = 10;
        var chanceToSpawnBoss = 1 + valueIncreasingWithTime;
        if (this.statsTicksSurvived < timeTillSpawnBoss) {
            // it is to early for boss
            return;
        }
        if (rand(0, 100) > chanceToSpawnBoss) {
            // not this time
            return;
        }
        var minBossPower = 2 + valueIncreasingWithTimeSlow;
        var maxBossPower = 7 + valueIncreasingWithTimeSlow;
        unit.power = rand(minBossPower,maxBossPower);
        unit.isBoss = true;
    }

    this.spawnBonus = function() {
        if (rand (1, 100) <= this.chanceToSpawnBonus) {
            var bonus = new TD.Bonus(this);
            this.currentMap.putBonusToMap(bonus);
        }
    }

    this.tick = function() {
        if (!this.running) {
            debug('Game will not tick anymore!');
            return;
        }

        this.newMap = new TD.Map(this.facet, this.mapConfig);
        // boost units
        // move units
        for (var unitId in this.units) {
            this.units[unitId].tick();
        }
        // all unit moved.
        this.duels();
        this.battles();
        this.handleBonuses();

        this.newMap.getBonuses(this.currentMap);
        this.currentMap = this.newMap;
        this.newMap = {};

        // Spawn for player
        this.spawnPlayerUnit();
        // Spawn Bonus
        this.spawnBonus();

        // Spawn for bot
        this.spawnBotUnit();
        this.currentMap.draw(this.units);
        this.checkLoseConditions();
        this.statsTicksSurvived++;
    }

    this.duels = function () {
        for (var unitId in this.units) {
            var unit1 = this.units[unitId];
            if (unit1.active) {
                var unitIdWasInCell = this.currentMap.get(unit1.x, unit1.y);
                if (unitIdWasInCell && this.units[unitIdWasInCell] !== undefined) {
                    var unit2 = this.units[unitIdWasInCell];
                    if (unit2.x == unit1.oldX && unit2.y == unit1.oldY) {
                        // DUEL BEGINS!
                        debug('Duel p1:' + unit1.power + ' p2:' + unit2.power);
                        if (unit1.power > unit2.power) {
                            var winner = unit1;
                            var loser  = unit2;
                        } else {
                            var winner = unit2;
                            var loser  = unit1;
                        }
                        if (unit1.owner == unit2.owner) {
                            // well actualy it is not a duel, but a union
                            winner.power = unit1.power + unit2.power;
                        } else {
                            // yea here they will actually battle!
                            winner.power = winner.power - loser.power;
                            this.statsBattle(winner, loser);
                        }
                        debug('remove unit duel');
                        this.removeUnit(loser);
                        if (winner.power == 0) {
                            debug('remove unit duel and winner');
                            this.removeUnit(winner);
                        }
                    }
                }
            }
        }
    }

    this.battles = function() {
        for (var unitId in this.units) {
            var unit1 = this.units[unitId];
            var existingUnitId = this.newMap.get(unit1.x, unit1.y);
            if (existingUnitId && this.units[existingUnitId] !== undefined) {
                // here will be battle

                var unit2 = this.units[existingUnitId];
                debug('Battle p1:' + unit1.power + ' p2:' + unit2.power);
                if (unit1.power > unit2.power) {
                    var winner = unit1;
                    var loser  = unit2;
                    this.newMap.setUnit(unit1, true);
                } else {
                    var winner = unit2;
                    var loser  = unit1;
                }
                if (unit1.owner == unit2.owner) {
                    // well actualy it is not a duel, but a union
                    winner.power = winner.power + loser.power;
                } else {
                    // yea here they will actually battle!
                    winner.power = winner.power - loser.power;
                    this.statsBattle(winner, loser);
                }
                debug('winner power:' + winner.power);
                this.removeUnit(loser);
                if (winner.power == 0) {
                    debug('remove unit battle and winner');
                    this.removeUnit(winner);
                }
            } else {
                debug ('set unit to map without battle p = ' + unit1.power);
                // there is no one to battle
                this.newMap.setUnit(unit1, true);
            }
        }
    }

    this.statsBattle = function(winner, loser) {
        if (winner.owner == 'player') {
            this.statsKilledUnits ++;
            this.statsPoints += this.pointsPerKill;
            if (winner.power == 0) {
                this.statsLostUnits ++;
            }
        } else {
            this.statsLostUnits ++;
            if (winner.power == 0) {
                this.statsKilledUnits ++;
                this.statsPoints += this.pointsPerKill;
            }
        }
        this.statsKilledPower += loser.power;
        this.statsLostPower   += loser.power;
        this.statsPoints      += loser.power
    }

    this.handleBonuses = function () {
        for(var key in this.currentMap.bonusesList) {
            var bonus = this.currentMap.bonusesList[key];
            var unitId = this.newMap.get(bonus.x, bonus.y);
            if (unitId && this.units[unitId] != undefined) {
                bonus.execute(this.units[unitId]);
            }
        }
    }

    this.userActionMoveUnit = function(unitId, direction) {
        debug('unit move');
        if (!this.checkUserUnit(unitId)) {
            return;
        }
        var unit = this.units[unitId];
        if (unit.direction == direction && unit.active == true) {
            this.facet.stopGame();
            debug('unit is already moving to this direction');
            return;
        }
        if (!isInt(direction) || direction < 0 || direction > 3) {
            this.facet.stopGame();
            debug('wrong direction provided by user (' + direction + ')');
            return;
        }
        // ok we save from any bullshit

        unit.direction = direction;
        unit.activate();
        this.tick();
    }

    this.userActionStopUnit = function (unitId) {
        this.checkUserUnit(unitId);
        var unit = this.units[unitId];
        unit.deactivate();
        this.tick();
    }

    this.checkUserUnit = function(unitId) {
        if (!unitId || this.units[unitId] == undefined) {
            this.facet.stopGame();
            debug('unit not found. unitId = ' + unitId);
            return false;
        }
        var unit = this.units[unitId];
        if (unit.owner != 'player') {
            this.facet.stopGame();
            debug('unit don`t belong to user. unitId = ' + unitId);
            return false;
        }
        return true;
    }

    this.checkLoseConditions = function () {
        var center = this.currentMap.getCenter();
        unitId = this.currentMap.get(center.x, center.y);
        if (unitId && this.units[unitId] !== undefined) {
            if (this.units[unitId].owner != 'player') {
                this.stop();
                $('#turnsSurvived').html(this.statsTicksSurvived);
                $('#unitsKilled').html(this.statsKilledUnits);
                $('#pointsEarned').html(this.statsPoints);

                $("#myModal").modal({                    // wire up the actual modal functionality and show the dialog
                    "backdrop"  : "static",
                    "keyboard"  : true,
                    "show"      : true                     // ensure the modal is shown immediately
                });

                var checkKey = $('#checkKey').val();

                Ajax.json('/MathEffect/save', {
                    //params : '__csrf=' + Ajax.getCSRF(),
                    data: 'turnsSurvived=' + this.statsTicksSurvived +
                        '&unitsKilled=' + this.statsKilledUnits +
                        '&pointsEarned=' + this.statsPoints +
                        '&checkKey=' + checkKey +
                        '&_token=' + $('#laravel-token').val()
                    //callBack : function(){Ajax.linkLoadingEnd(link)}
                });
            }
        }
    }

    this.stop = function () {
        this.running = false;
    }
}
