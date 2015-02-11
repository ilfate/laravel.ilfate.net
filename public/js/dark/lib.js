/**
 * Created by Ilya Rubinchik (ilfate) on 21/01/15.
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

function IL () {

}
IL = new IL();


IL.TextAnimator = function() {
	this.target = {};
	this.text   = '';
	this.effect = {};

	this.setTarget = function(target) {
		this.target = target;
	}
	this.setText = function(text) {
		this.text = text;
	}
	this.setEffect = function(effect) {
		this.effect = effect;
		this.effect.setAnimator(this);
	}
	this.render = function() {
		this.effect.render();
	}
}



function Effect () {

}
Effect = new Effect();

Effect.Lamp = function() {
	this.animator = {};
	this.delays = [1000,50, 500,50, 50,80, 400,100, 50,100, 200];
	this.currentStep = 0;
	this.isOn = false;
	this.setAnimator = function(animator) {
		this.animator = animator;
	}
	this.render = function() {
		if (this.delays[this.currentStep]) {
			var _this = this;
			setTimeout(function() {_this.runLamp();}, this.delays[this.currentStep]);
		}
	}

	this.runLamp = function() {
		if (this.isOn) {
			this.hide();
		} else {
			this.show();
		}
		this.currentStep++;
		this.render();
	}
	this.show = function() {
		this.animator.target.html(this.animator.text)
			.css('opacity', 1);
		this.isOn = true;
	}
	this.hide = function () {
		this.animator.target.css('opacity', 0.2);	
		this.isOn = false;
	}
}