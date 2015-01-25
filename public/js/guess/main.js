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
    this.countImagesToLoad = 0;
    this.resultK = 0;
    this.resultSec = 0;
    this.correctAnswer = false;
    this.gameFinished = false;
    this.userName = false;

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
	}

    this.startTurn = function(isFirstTurn) {
        this.currentAnswer = 'none';
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
        switch (question.type) {
            case 1:
                $('.answer .block').on({
                    mouseenter: function(){Guess.Game.answerButtonMouseOver($(this))},
                    mouseleave: function(){Guess.Game.answerButtonMouseOut($(this))}
                });
                break
            case 2:
                $('.answer').on({
                    mouseenter: function(){Guess.Game.answerImageMouseOver($(this))},
                    mouseleave: function(){Guess.Game.answerImageMouseOut($(this))}
                });
                break;
        }

	}

    this.sendAnswer = function(el) {
        window.clearInterval(this.timerInterval);
        var dSec =  this.checkTime();
        if (this.secondsLeft > this.currentQuestion.sec - dSec + 2) {
            this.secondsLeft = this.currentQuestion.sec - dSec;
        }
        if (!dSec) {
            return ;
        }
        var id = el.data('id');
        this.currentAnswer = id;

        url = '/GuessGame/answer';
        Ajax.json(url, {
            data: 'id=' + id + '&seconds=' + this.secondsLeft,
            callBack : function(data){Guess.Game.result(data)}
        });
        this.animateAnswerSent();
    }

	this.result = function(data) {
        if (data.finish !== undefined) {
            this.gameFinished = true;
            this.pointsAmount = data.points;
            this.correctAnswersNumber = data.correctAnswersNumber;
            this.correctAnswer = data.correctAnswer;
            this.userName = data.name;
            if (this.nextAnimationsEnded) {
                this.showFalseAnswerAnimation();
            } else {
                this.nextImagesLoaded = true;
            }
        } else {
            this.currentTurn++;
            //this.showQuestionResult(data.result.k, data.result.seconds);
            this.resultK = data.result.k;
            this.resultSec = data.result.seconds;
            this.currentQuestion = data.question;
            this.prepareToStartTurn(data.question);

        }
	}
    this.timeIsOut = function(data) {
        this.pointsAmount = data.points;
        this.correctAnswer = data.correctAnswer;
        this.correctAnswersNumber = data.correctAnswersNumber;
        this.userName = data.name;
        this.showFalseAnswerAnimation();
    }

    this.prepareToStartTurn = function(question) {
        var urls = [];
        path = '/images/game/guess/';
        switch (question.type) {
            case 1:
                urls.push(path+question.picture);
                this.countImagesToLoad = 1;
                break;
            case 2:
                urls.push(path+question.options[0]);
                urls.push(path+question.options[1]);
                urls.push(path+question.options[2]);
                urls.push(path+question.options[3]);
                this.countImagesToLoad = 4;
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
        Guess.Game.countImagesToLoad--;
        if (Guess.Game.countImagesToLoad == 0) {
            Guess.Game.nextImagesLoaded = true;
        }
        if (Guess.Game.nextImagesLoaded && Guess.Game.nextAnimationsEnded) {
            Guess.Game.showQuestionResult();
        }
    }
    this.answerAnimationEnded = function() {
        this.nextAnimationsEnded = true;
        if (this.nextImagesLoaded && this.nextAnimationsEnded) {
            if (this.gameFinished) {
                this.showFalseAnswerAnimation();
            } else {
                Guess.Game.showQuestionResult();
            }
        }
    }

    this.animateAnswerSent = function() {
        var correctAnswerEl = $('.answer.id-' + this.currentAnswer);
        if (correctAnswerEl.hasClass('name')) {
            $('.answer .block').off();
            correctAnswerEl.find('block').css({'background-color':'#069E2D'});
        }
        this.hideOtherAnswers(this.currentAnswer);
        setTimeout(function(){Guess.Game.answerAnimationEnded()}, 300);
    }
    this.hideOtherAnswers = function(otherFromThat) {
        for(var i = 0; i < 4; i++) {
            var answerEl = $('.answer.id-' + i);

            if (i !== otherFromThat) {
                if (answerEl.hasClass('name')) {
                    answerEl.find('.block').css({'background-color':'#FFD416'});
                }
                answerEl.animate({
                    opacity:0
                }, 300);
            }
        }
    }

    this.showQuestionResult = function() {
        var k = this.resultK;
        var sec = this.resultSec;
        $('.question').animate({opacity:0}, {duration:300, complete:function(){Guess.Game.startTurn(false)}});
        var points = k * sec;
        if (points != parseInt(points)) {
            points = Math.round(points*10)/10
        }
        this.pointsAmount += points;
        if (this.pointsAmount != parseInt(this.pointsAmount)) {
            this.pointsAmount = Math.round(this.pointsAmount*10)/10;
        }
        $('.add-points').delay(150)
            .html(points)
            .show()
            .css({right:'-30px', opacity:1})
            .animate({right:'40%', opacity:0.7},
            {duration:500, complete:function(){
                $('.points-amount').html(Guess.Game.pointsAmount).css({'font-size':'36px', color:'#FFD416'}).animate({'font-size':'20px', color:'#000000'}, 500);
                $(this).hide();
            }});
    }

    this.showFalseAnswerAnimation = function() {
        var duration = 1200;
        if (this.currentAnswer != 'none') {
            var currentAnswerEl = $('.answer.id-' + this.currentAnswer);
            if (currentAnswerEl.hasClass('name')) {
                currentAnswerEl.find('.block').animate({'background-color':'#F21616'}, duration);
            } else {
                currentAnswerEl.animate({'opacity':0.4}, duration);
            }
        } else {
            this.hideOtherAnswers(this.correctAnswer);
        }
        var correctAnswerEl = $('.answer.id-' + this.correctAnswer);

        correctAnswerEl.animate({opacity:1}, {'duration':duration, complete:function(){Guess.Game.showEndModal()}});
        if (correctAnswerEl.hasClass('name')) {
            correctAnswerEl.find('.block').animate({'background-color':'#069E2D'}, duration);
            $('.answer .block').off();
        }
    }

    this.showEndModal = function() {
        $('.turn-area, .col-md-10.timer').animate({opacity:0}, {duration:300, complete:function(){
            $('.turn-area, .col-md-10.timer').hide(300);
        }});//,
        $('.points-container').delay(600).animate({'width':'100%'}, 300);
        $('.points-amount').delay(900).animate({'color':'#ffffff'}, 600);
        $('.points').delay(900).animate(
            {
                'height':'542px',
                'padding-top':'100px',
                'margin-top':'20px',
                'background-color':'#428bca',
                'color':'#ffffff'
            }, {duration:600, complete:function(){
                $('.points').addClass('points-modal');
                Guess.Game.fillEndModal();
            }});
    }

    this.fillEndModal = function() {
        var answersText = 'You gave ' + this.correctAnswersNumber + ' answers';
        $('.points-amount').html('<span class="left"></span><span class="number">' + this.pointsAmount + '</span><span class="right"></span>');
        $('.points-amount').after('<br><span class="modal-user-name"></span><br>');
        $('.points-amount').after('<br><span class="rest-stats-text"></span>');
        $('.restart-button').show();
        $('.points-amount .number').animate({'font-size':'40px'}, 800);
        $('.points-amount').animate({'font-size':'30px'}, 800);
        var queue = [
            {text:' points', el:$('.points-amount .right'), options:{'duration':400}},
            {text:answersText, el:$('.rest-stats-text'), options:{'duration':800}}
        ];
        if (this.userName) {
            queue.push({text:'Saved for name: ' + this.userName, el:$('.modal-user-name'), options:{'duration':500}})
        }
        $('.rest-stats-text').delay(900).animate({'font-size':'30px'}, 800);
        pasteText('You earned ', $('.points-amount .left'), {'duration':500, 'queue':queue});
        if (!this.userName) {
            var template = $('#template-end-modal').html();
            Mustache.parse(template);
            var rendered = Mustache.render(template, {'data' : {'answers':this.correctAnswersNumber}});
            var obj = $(rendered);
            $('.restart-button').before(obj);
            Ajax.init();
        }
    }

    this.hideNameForm = function() {
        $('.end-modal').hide();
        pasteText('Results saved :)', $('.modal-user-name'), {'duration':500});
    }

    this.timerTick = function() {
        this.secondsLeft--;
        var dSec = this.checkTime();

        var percent = 100 - dSec / (this.currentQuestion.sec / 100);
        $('.timer .progress-bar').css({'width' : percent + '%'});
        $('.timer .seconds .text').html(this.secondsLeft);


    }
    this.checkTime = function() {
        var currentTime = new Date();
        var dSec = (currentTime.getTime() - this.turnStartTime.getTime()) / 1000;
        if (dSec > this.currentQuestion.sec) {
            this.gameFinished = true;
            url = '/GuessSeries/timeIsOut';
            Ajax.json(url, {
                callBack : function(data){Guess.Game.timeIsOut(data)}
            });
            window.clearInterval(this.timerInterval);
            $('.answer').off('click');
            return 0;
        } else {
            return dSec;
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

    this.answerImageMouseOver = function(el) {
        el.animate({'background-size': '80%'}, {
            queue:false,
            duration:400
        });
    }
    this.answerImageMouseOut = function(el) {
        el.stop();
        el.css({'background-size': '100%'});
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


function pasteText(text, el, options) {
    if (options && options.duration !== undefined) {
        if (text.length == 1) {
            var time = options.duration;
        } else {
            var time = parseInt((options.duration / text.length)* (rand(8,12)/10));
            options.duration -= time;
        }
    } else {
        var time = rand(10,80);
    }
    var letter = text.substr(0, 1);
    var rest = text.substr(1);
    el.append(letter);
    if (text.length > 1) {
        setTimeout(function(){pasteText(rest, el, options);}, time);
    } else {
        if (options && options.queue !== undefined) {
            var data = options.queue[0];
            options.queue.shift();

            if (data.options == undefined) {
                data.options = {};
            }
            if (options.queue.length > 0) {
                data.options.queue = options.queue;
            }
            pasteText(data.text, data.el, data.options);
        }
    }
}