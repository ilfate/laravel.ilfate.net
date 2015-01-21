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

function Guess () {

}
Guess = new Guess();

$(document).ready(function() {
    Guess.Game = new Guess.Game();


});


Guess.Game = function () {
	this.nextQuestion = [];
	this.questionNumber = 1;

	this.init = function(firstQuestion) {
		this.nextQuestion = firstQuestion;
		$('#start-game').on({
            click : function(){ Guess.Game.startGame(); }
        });
	}

	this.startGame = function() {
		// animation will be here
		$('.game-container').show();
		$('#start-game').hide();

		// show wait gif

		var alreadyLoadedQuestion = this.getNextQuestion();
		if (alreadyLoadedQuestion) {
			this.startTurn(alreadyLoadedQuestion);
		}
	}

	this.getNextQuestion = function() {
		if (this.questionNumber == 1 && this.nextQuestion) {
			return this.nextQuestion;
		}
		url = '/GuessGame/getQuestion';
		Ajax.json(url, {
            callBack : function(data){Guess.Game.questionRecived(data)}
        });
	}

	this.drawQuestion = function(question) {
		switch (question.type) {
			case 1:
				var temaplate = $('#template-single-picture').html();
				break;
		}
		
		Mustache.parse(temaplate);   // optional, speeds up future uses
        var rendered = Mustache.render(temaplate, {'question' : question});
        var obj = $(rendered);
        $('.turn-area').append(obj);
	}

	this.questionRecived = function(data) {
		
	}

	this.startTurn = function(question) {
		this.drawQuestion(question);
	}
}