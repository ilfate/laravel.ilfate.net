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

function TCG () {

}
TCG = new TCG();

$(document).ready(function() {
	TCG.Game = new TCG.Game();
	

});


TCG.Game = function () {

    this.width = 5;
    this.height = 5;
	this.cardInFocus;
    this.isMyTurn = true;
	this.phase = 0;

	this.fuu = function() {
		info('awdawd');
	}

	this.init = function(data) {
		this.phase = data.phase;

		this.bindObjects();
	}

	this.bindObjects = function()
	{
		$('.hand .my-card').bind('click', function(){ TCG.Game.event('cardClick', $(this)) });
        $('.field .cell').bind('click', function(){ TCG.Game.event('cellClick', $(this)) });
	}

	this.event = function(name, obj) {
		switch(name) {
			case 'cardClick':
				if(obj.hasClass('card')) {
					this.cardClick(obj);
                    switch(this.phase) {
                        case 3: // Deploy phase
                            // light them up!
                            if (this.cardInFocus) {
                                this.lightUpDeployArea();
                            } else {
                                this.unFocusDeployArea();
                            }
                            break;
                    }
				}
			break;
            case 'cellClick':
                this.deploy(obj);
                break;
		}
	}

    this.deploy = function(cell) {
        if (this.cardInFocus && this.phase == 3 && this.isMyTurn) {
            var x = cell.data('x');
            var y = cell.data('y');
            var cardId = this.cardInFocus.data('id');

            window.location = "/tcg/action?action=deploy&cardId=" + cardId + "&x=" + x + "&y=" + y;
        }
    }

    this.lightUpDeployArea = function() {
        $('.field .cell.y_' + (this.height-2) + ', .field .cell.y_' + (this.height-1)).addClass('focus')
    }

    this.unFocusDeployArea = function() {
        $('.field .cell.focus').removeClass('focus');
    }

	this.cardClick = function(obj) {
		if(obj.hasClass('focus')) {
			obj.removeClass('focus');
            this.cardInFocus = false;
		} else {
			if (this.cardInFocus) {
				this.cardInFocus.removeClass('focus');
			}
			obj.addClass('focus');
			this.cardInFocus = obj;
		}
	}

}