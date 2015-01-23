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
    this.currentTurn = 1;
    this.pointsAmount = 0;
    this.timerInterval = 0;

    this.isTimeLimited = true;
    this.turnStartTime = {};
    this.secondsLeft = 0;

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
        window.clearInterval(this.timerInterval);
        var id = el.data('id');
        info('id to send = ' + id);

        url = '/GuessGame/answer';
        Ajax.json(url, {
            data: 'id=' + id + '&seconds=' + this.secondsLeft,
            callBack : function(data){Guess.Game.result(data)}
        });
    }

	this.result = function(data) {
		info(data);
        if (data.finish !== undefined) {
            window.location = "/GuessSeries";
        } else {
            this.currentTurn++;
            this.showQuestionResult(data.result.k, data.result.seconds);
            this.startTurn(data.question)
        }
	}

	this.startTurn = function(question) {
        $('.timer .progress-bar').css({'width' : '100%'});
        this.secondsLeft = question.sec;
        $('.timer .seconds .text').html(this.secondsLeft);

        this.currentQuestion = question;
        this.turnStartTime   = new Date();
		this.drawQuestion(question);
	}

    this.showQuestionResult = function(k, sec) {
        var points = k * sec;
        this.pointsAmount += points;
        $('.points-amount').html(this.pointsAmount);
    }

    this.timerTick = function() {
        this.secondsLeft--;
        var currentTime = new Date();
        var dSec = (currentTime.getTime() - this.turnStartTime.getTime()) / 1000;
        var percent = 100 - dSec / (this.currentQuestion.sec / 100);
        $('.timer .progress-bar').css({'width' : percent + '%'});
        $('.timer .seconds .text').html(this.secondsLeft);

        if (dSec > this.currentQuestion.sec) {
            info('time is ower');
            window.clearInterval(this.timerInterval);
            $('.answer').off('click');
        }
    }
}