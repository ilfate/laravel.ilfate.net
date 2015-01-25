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
    this.queue = {};

    this.currentQuestion = {};
    this.currentTurn = 1;
    this.currentAnswer = 0;
    this.pointsAmount = 0;
    this.timerInterval = 0;

    this.nextImagesLoaded = false;
    this.nextAnimationsEnded = false;

    this.isTimeLimited = true;
    this.turnStartTime = {};
    this.secondsLeft = 0;

	this.init = function(firstQuestion) {
		this.currentQuestion = firstQuestion;
		$('#start-game').on({
            click : function(){ Guess.Game.startGame(); }
        });
        this.drawQuestion(firstQuestion);
	}

	this.startGame = function() {
        url = '/GuessGame/gameStarted';
        Ajax.json(url, {});
		// animation will be here
		//$('.game-container').show();
		$('#start-game').animate({
            backgroundColor: "#abcdef",
            width:'100%'
        }, 500, function() {
            $('.game-container').fadeIn(500);
            Guess.Game.startTurn(true);
        } );
        $('#start-game').animate({
            backgroundColor: "#abcdef",
            height:'500px',
            top:'0',
            opacity:0,
            'line-height':'250px'
        }, 500, function(){
            $('#start-game').remove();
        });
		// show wait gif
		//this.startTurn(true);

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
		Mustache.parse(temaplate);
        var rendered = Mustache.render(temaplate, {'question' : question});
        var obj = $(rendered);
        $('.turn-area').hide();
        $('.turn-area').append(obj);
        $('.turn-area').fadeIn(250);
        $('.answer').on({
            click : function(){ Guess.Game.sendAnswer($(this)); }
        });
        $('.answer .block').on({
            mouseenter: function(){Guess.Game.answerButtonMouseOver($(this))},
            mouseleave: function(){Guess.Game.answerButtonMouseOut($(this))}
        });
	}

    this.sendAnswer = function(el) {
        window.clearInterval(this.timerInterval);
        var id = el.data('id');
        this.currentAnswer = id;
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
            this.currentQuestion = data.question;
            this.prepareToStartTurn(data.question);

        }
	}

    this.prepareToStartTurn = function(question) {
        var urls = [];
        path = '/images/game/guess/';
        switch (question.type) {
            case 1:
                urls.push(path+question.picture);
                break;
            case 2:
                urls.push(path+question.options[0]);
                urls.push(path+question.options[1]);
                urls.push(path+question.options[2]);
                urls.push(path+question.options[3]);
                break;
        }
        var images = $('<div></div>').addClass('to-delete').hide();
        for (var i in urls) {
            images.append('<img src="' + urls[i] + '"/>');
        }
        $('body').append(images);
        images.imagesLoaded().progress(Guess.Game.imagesLoaded);
    }

    this.imagesLoaded = function() {
        Guess.Game.nextImagesLoaded = true;
        if (this.nextImagesLoaded && this.nextAnimationsEnded) {
            Guess.Game.startTurn(false);
        }
    }
    this.answerAnimationEnded = function() {
        this.nextAnimationsEnded = true;
        if (this.nextImagesLoaded && this.nextAnimationsEnded) {
            Guess.Game.startTurn(false);
        }
    }

	this.startTurn = function(isFirstTurn) {
        this.nextImagesLoaded = false;
        this.nextAnimationsEnded = false;
        var question = this.currentQuestion;

        $('.timer .progress-bar').css({'width' : '100%'});
        this.secondsLeft = question.sec;
        $('.timer .seconds .text').html(this.secondsLeft);
        //.animate({color:'#F21616'}, question.sec * 1000);
        $('.timer .seconds').animate({'background-color':'#F21616'}, question.sec * 1000);

        this.turnStartTime   = new Date();
        if (!isFirstTurn) {
            this.drawQuestion(question);
            $('.to-delete').remove();
        }
        this.timerInterval = setInterval(function() { Guess.Game.timerTick() }, 1000);
	}

    this.showQuestionResult = function(k, sec) {
//        var positions = [
//            {left:0},
//            {right:0},
//            {left:0,top:'100px'},
//            {top:'100px', right:0},
//        ];
        var correctAnswerEl = $('.answer.id-' + this.currentAnswer);
        var width = correctAnswerEl.width();
        $('.answer .block').off();
//        $('.answer').css({
//            position: 'absolute',
//            width:width+'px',
//            padding:0,
//        })
        correctAnswerEl.find('block').css({'background-color':'#069E2D'});

        for(var i = 0; i < 4; i++) {
            var answerEl = $('.answer.id-' + i);
//            answerEl.css(positions[i]);

            if (i !== this.currentAnswer) {
                if (answerEl.hasClass('image')) {
                    answerEl.css({'background-color':'#FFD416'});
                } else {
                    answerEl.find('.block').css({'background-color':'#FFD416'});
                }

                answerEl.animate({
                    opacity:0
                }, 300);
            }
        }
        $('.question').delay(250).animate({opacity:0}, {duration:300, complete:function(){Guess.Game.answerAnimationEnded()}})
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

    this.answerButtonMouseOver = function(el) {
        $('.answer .block').css({'background-color':'#abcdef'});
        el.bounce();
        el.animate({'background-color':'#069E2D'}, {
            queue:false,
            duration:400
        });
    }
    this.answerButtonMouseOut = function(el) {
        el.stop();
        el.css({'background-color':'#abcdef'});
    }


    ///// ADMIN

    this.adminDuffeculty = function(el) {
        el = $(el);
        $('.difficulty-input').val(el.val());
    }
    this.seriesImagesGenerate = function(seriesId) {
        url = '/GuessSeries/admin/generateImages';
        Ajax.json(url, {
            data: 'seriesId=' + seriesId
        });
    }
}