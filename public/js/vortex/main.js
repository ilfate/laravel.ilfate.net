/**
 * Created by Ilya Rubinchik (ilfate) on 18/04/15.
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

function Vortex () {

}
Vortex = new Vortex();

$(document).ready(function() {
    Vortex.Game = new Vortex.Game();


});

Vortex.Game = function () {
	this.CONST_ACCESSIBLE = 'status-accessible';
	this.CONST_VISIBLE    = 'status-visible';
	this.CONST_ACTIVATED  = 'status-activated';
	this.isFirstAction = true;

	this.init = function() {
		$('.map .cells .cell').on({'click':function(){
			Vortex.Game.cellClick($(this));
		}})
	}

	this.cellClick = function(el) {
		var x = el.data('x');
		var y = el.data('y');
		this.sendAction(x, y);
	}

	this.sendAction = function(x, y) {
		url = '/Vortex/action';
        Ajax.json(url, {
            data: 'x=' + x + '&y=' + y,
            callBack : function(data){Vortex.Game.result(data)}
        });
	}

	this.result = function(data) {
		info(data);
		if (this.isFirstAction) {
			$('.map .cells .cell').removeClass(this.CONST_ACCESSIBLE);
			this.isFirstAction = false;
		}
		for (var x in data.map) {
			for (var y in data.map[x]) {
				var cellObj = $('.map .cells .cell.x-' + x + '.y-' + y);
				if (data.map[x][y].visible && !cellObj.hasClass(this.CONST_VISIBLE)) {
					cellObj.addClass(this.CONST_VISIBLE);
				}
				if (data.map[x][y].accessible && !cellObj.hasClass(this.CONST_ACCESSIBLE)) {
					cellObj.addClass(this.CONST_ACCESSIBLE);
				}
				if (data.map[x][y].activated && !cellObj.hasClass(this.CONST_ACTIVATED)) {
					cellObj.addClass(this.CONST_ACTIVATED);
				}				
				this.showIcon(cellObj, data.map[x][y]);
			}
		}

	}

	this.showIcon = function(el, data) {
		if (data.visible || data.activated) {
			var type = data.eventId;
			if (data.eventTypeId) {
				type += '_' + data.eventTypeId;
			}
		}
		var html = '';
		switch (type) {
			case '1_1':	html = '<i class="fa fa-arrow-up"></i>'; break;
			case '1_2':	html = '<i class="fa fa-arrow-right"></i>'; break;
			case '1_3':	html = '<i class="fa fa-arrow-down"></i>'; break;
			case '1_4':	html = '<i class="fa fa-arrow-left"></i>'; break;
			case '2_1':
			case '2_2':
			case '2_3':
			case '2_4':
			case '2_5':
			case '2_6':
			case '2_7':
			case '2_8':
			case '2_9':
				html = '<i class="fa fa-eye"></i>';
				break;
		}
		if (html) {
			el.html(html);
		}
	}

};