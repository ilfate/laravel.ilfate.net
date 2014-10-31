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

	this.cardInFocus;
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
		$('.hand .my-card').bind('click', function(){ TCG.Game.event('click', $(this)) });
	}

	this.event = function(name, obj) {
		switch(name) {
			case 'click':
				if(obj.hasClass('card')) {
					this.cardClick(obj);
				}
			break;
		}
	}

	this.cardClick = function(obj) {
		if(obj.hasClass('focus')) {
			obj.removeClass('focus');
		} else {
			if (this.cardInFocus) {
				this.cardInFocus.removeClass('focus');
			}
			obj.addClass('focus');
			this.cardInFocus = obj;
		}
	}

}