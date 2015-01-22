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

    this.currentQuestion = {};
    this.timerInterval = 0;

    this.isTimeLimited = true;
    this.turnStartTime = {};

	this.init = function(firstQuestion) {
		this.nextQuestion = firstQuestion;
		$('#start-game').on({
            click : function(){ Guess.Game.startGame(); }
        });
	}

	this.startGame = function() {
        url = '/GuessGame/gameStarted';
        Ajax.json(url, {});
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
        $('.turn-area').html('');
		switch (question.type) {
			case 1:
				var temaplate = $('#template-single-picture').html();
				break;
            case 2:
                var temaplate = $('#template-four-pictures').html();
                break;
		}
		info(question);
		Mustache.parse(temaplate);   // optional, speeds up future uses
        var rendered = Mustache.render(temaplate, {'question' : question});
        var obj = $(rendered);
        $('.turn-area').append(obj);

        $('.answer').on({
            click : function(){ Guess.Game.sendAnswer($(this)); }
        });
        this.timerInterval = setInterval(function() { Guess.Game.timerTick() }, 1000);
	}

    this.sendAnswer = function(el) {
        var id = el.data('id');
        info('id to send = ' + id);

        url = '/GuessGame/answer';
        Ajax.json(url, {
            data: 'id=' + id,
            callBack : function(data){Guess.Game.result(data)}
        });
    }

	this.result = function(data) {
		info(data);
        if (data.finish !== undefined) {
            window.location = "/GuessSeries";
        } else {
            this.startTurn(data.question)
        }
	}

	this.startTurn = function(question) {
        $('.timer .seconds').css({'width' : '100%'});
        this.currentQuestion = question;
        this.turnStartTime = new Date();
		this.drawQuestion(question);
	}

    this.timerTick = function() {
        var currentTime = new Date();
        var dSec = (currentTime.getTime() - this.turnStartTime.getTime()) / 1000;
        var percent = 100 - dSec / (this.currentQuestion.sec / 100);
        $('.timer .seconds').css({'width' : percent + '%'});
        if (dSec > this.currentQuestion.sec) {
            info('time is ower');
            window.clearInterval(this.timerInterval);
            $('.answer').off('click');
        }
    }
}