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
function is_object(obj) {
    return typeof obj === 'object';
}

function TCG () {

}
TCG = new TCG();

$(document).ready(function() {
	TCG.Game = new TCG.Game();
	

});


TCG.Game = function () {

    this.width = 5;
    this.height = 5;
	this.handCardInFocus;
	this.fieldCardInFocus;
    this.isMyTurn = true;
	this.phase = 0;
    this.currentPlayerId;
    this.units = new TCG.Units(this);

	this.fuu = function() {
		info('awdawd');
	}

	this.init = function(data) {
		this.phase = data.phase;
        this.isMyTurn = data.isMyTurn;
        this.currentPlayerId = data.playerId;
        if (this.phase == 4 && this.isMyTurn) {
            this.markMoveForCardId(data.card);
        }
        
		this.bindObjects();
        this.units.init();
	}

	this.bindObjects = function()
	{
		$('.hand .my-card').live('click', function(){ TCG.Game.event('cardClick', $(this)) });
        $('.field .cell').live('click', function(){ TCG.Game.event('cellClick', $(this)) });
        $('.field .unit').live('click', function(){ TCG.Game.event('unitClick', $(this)) });
        $('.field .cell .skip').live('click', function(){ TCG.Game.event('skip', $(this)) });
        
	}

	this.event = function(name, obj) {
		switch(name) {
			case 'cardClick':
				if(obj.hasClass('card')) {
					this.cardClick(obj);
                    switch(this.phase) {
                        case 3: // Deploy phase
                            // light them up!
                            if (this.handCardInFocus) {
                                this.lightUpDeployArea();
                            } else {
                                this.unFocusDeployArea();
                            }
                            break;
                        case 4: // battle
                            this.toggleCastButton(obj);
                            // we need to hide current focused cells for unit
                            break;
                    }
				}
			break;
            case 'cellClick':
                if (this.phase == 3) {
                    this.deploy(obj);
                } else if (this.phase == 4) {
                    if (!this.handCardInFocus) {
                        this.moveUnit(obj);
                    } else {
                        this.spell(obj, 'cell');
                    }
                }
                break;
            case 'unitClick':
                if (this.phase == 4) {
                    this.spell(obj, 'unit');
                }
                break;
            case 'skip' :
                this.skip();
                break
		}
	}

    this.action = function(data) {
        switch(data.type) {
//            case 'deploy':
//
//                break;
            default:
                var type = 'get';
                var url = '/tcg/action?';
                var first = true;
                for(var key in data.data) {
                    var value = data.data[key];

                    if (is_object(value)) {
                        for (var key2 in value) {
                            url += '&' + key + '[' + key2 + ']=' + value[key2]
                        }
                    } else {
                        if (first) {
                            first = false;
                        } else {
                            url += '&';
                        }
                        url += key + '=' + value;
                    }
                }
                break;
        }
        if (data.type == 'deploy') {
            type = 'ajax';
        }
            url += '&playerId=' + this.currentPlayerId;
        switch (type) {
            case 'get':
                window.location = url;
                break;
            case 'ajax':
                Ajax.json(url, {
                    //params : '__csrf=' + Ajax.getCSRF(),
//                    data: 'turnsSurvived=' + this.statsTicksSurvived +
//                        '&unitsKilled=' + this.statsKilledUnits +
//                        '&pointsEarned=' + this.statsPoints +
//                        '&checkKey=' + checkKey +
//                        '&_token=' + $('#laravel-token').val()
                    callBack : function(data){TCG.Game.processLog(data)}
                });
                break;
        }

    }

    this.deploy = function(cell) {
        if (this.handCardInFocus && this.phase == 3 && this.isMyTurn) {
            var x = cell.data('x');
            var y = cell.data('y');
            var cardId = this.handCardInFocus.data('id');

            //this.action("/tcg/action?action=deploy&cardId=" + cardId + "&x=" + x + "&y=" + y);
            this.action({'type' : 'deploy', 'data' : {'action' : 'deploy', 'cardId' : cardId, 'x' : x, 'y' : y}});
        }
    }

    this.moveUnit = function(cell) {
        if (!this.handCardInFocus && this.fieldCardInFocus && this.phase == 4 && this.isMyTurn && cell.hasClass('focus')) {
            var x = cell.data('x');
            var y = cell.data('y');
            var cardId = this.fieldCardInFocus.data('id');

            this.action({'type' : 'move', 'data' : {'action' : 'move', 'cardId' : cardId, 'x' : x, 'y' : y}});
            //this.action("/tcg/action?action=move&cardId=" + cardId + "&x=" + x + "&y=" + y);
        } else {
            if (this.handCardInFocus && this.phase == 4 && this.isMyTurn) {
                alert('You are trying to move unit, but you have card in hand active');
            }
        }
    }

    this.spell = function(obj, type) {
        if (this.handCardInFocus && this.phase == 4 && this.isMyTurn) {
            var spellType = this.handCardInFocus.data('spelltype');
            if (type != spellType) {
                info(type);
                info ('you are trying to cast spell? target is wrong')
                return ;
            }
            var cardId = this.handCardInFocus.data('id');
            if (spellType == 'unit') {
                var targetId = obj.data('id');
                this.action({'type' : 'cast', 'data' : {'action' : 'cast', 'cardId' : cardId, 'data' : {'targetId' : targetId}}});
                //this.action("/tcg/action?action=cast&cardId=" + cardId + "&data[targetId]=" + targetId);
            } else if (spellType == 'cell') {
                var x = obj.data('x');
                var y = obj.data('y');
                this.action({'type' : 'cast', 'data' : {'action' : 'cast', 'cardId' : cardId, 'data' : {'x' : x, 'y' : y}}});
                //this.action("/tcg/action?action=cast&cardId=" + cardId + "&data[x]=" + x + "&adata[y]=" + y);
            } else if(spellType == 'cast') {
                this.action({'type' : 'cast', 'data' : {'action' : 'cast', 'cardId' : cardId, 'data' : {}}});
                //this.action(window.location = "/tcg/action?action=cast&cardId=" + cardId + "&data[]=");
            }
        }
    }

    this.skip = function() {
        if (this.isMyTurn) {
            this.action({'type' : 'skip', 'data' : {'action' : 'skip'}});
            this.action("/tcg/action?action=skip");
        }
    }

    this.processLog = function(log)
    {
        log = log.log;
        for(var i in log) {
            var event = log[i];
            switch(event.type) {
                case 'deploy':
                    this.units.deploy(event.playerId, event.card);
                    break;
            }
        }
    }

    this.markMoveForCardId = function(cardId) {
        this.fieldCardInFocus = $('.field .unit.id_' + cardId);
        this.fieldCardInFocus.addClass('focus');
        //var cell = $(this.fieldCardInFocus.parent());
        var x = this.fieldCardInFocus.data('x');
        var y = this.fieldCardInFocus.data('y');
        var neibours = this.getNeiboursCells(x, y);
        for (var key in neibours) {
            var dx = neibours[key][0];
            var dy = neibours[key][1];
            if (dx >= 0 && dy >= 0 && dx < this.width && dy < this.height) {

                if (!$('.field .unit.x_' + dx + '.y_' + dy).length) {
                    // this cell is free
                    var newCell = $('.field .cell.x_' + dx + '.y_' + dy);
                    newCell.addClass('focus');
                }

            }
        }
    }

    this.lightUpDeployArea = function() {
        $('.field .cell.y_' + (this.height-2) + ', .field .cell.y_' + (this.height-1)).addClass('focus')
    }

    this.unFocusDeployArea = function() {
        $('.field .cell.focus').removeClass('focus');
    }

    this.toggleCastButton = function(obj) {
        if (this.handCardInFocus && obj.data('spelltype') == 'cast') {
            info(obj.find('.cast'));
        } else {
            obj.find('.cast').hide();
        }
    }

	this.cardClick = function(obj) {
		if(obj.hasClass('focus')) {
			obj.removeClass('focus');
            this.handCardInFocus = false;
		} else {
			if (this.handCardInFocus) {
				this.handCardInFocus.removeClass('focus');
			}
			obj.addClass('focus');
			this.handCardInFocus = obj;
		}
	}

    this.getNeiboursCells = function(x, y) {
        return [
            [x - 1, y],
            [x + 1, y],
            [x, y - 1],
            [x, y + 1],
        ];
    }

}