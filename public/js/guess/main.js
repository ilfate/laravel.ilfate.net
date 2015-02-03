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
    this.color = {
        'blue' : '#428BCA',
        'green' : '#069E2D',
        'yellow' : '#FFD416',
        'red' : '#F21616',
        'orange' : '#EF8354',
        'black' : '#584D3D',
        'white' : '#FFFFFF'
    };
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
    this.stats = [];

    this.pointsAnimationEnabled = true;
    this.isSwitchImages = false;

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
        url = '/GuessSeries/gameStarted';
        Ajax.json(url, {});
        //$('.ability').show();
        $('.sidebar-on-start-buttons').fadeOut(1000, function(){$(this).remove()});
        $('.ability').animate({opacity:1},1000);
        $('.ability').on({
            'click':function(){Guess.Game.ability($(this))}
        })
		// animation will be here
		//$('.game-container').show();
		$('#start-game').animate({
            backgroundColor: "#529BCA",
            width:'100%'
        }, 500, function() {
            $('.game-container').fadeIn(500);
            Guess.Game.startTurn(true);
        } );
        $('#start-game').animate({
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

        this.restartTimer();

        if (!isFirstTurn) {
            this.drawQuestion(question);
            $('.to-delete').remove();
        }
    }
    this.switchImagesAction = function() {
        this.nextImagesLoaded = false;
        this.nextAnimationsEnded = false;
        this.isSwitchImages = false;

        switch(this.currentQuestion.type) {
            case 1:
                $('.single-picture-overlay').css('background-image', $('.single-picture').css('background-image'));
                $('.single-picture').css({'background-image': 'url("/images/game/guess/' + this.currentQuestion.picture + '")'});
                $('.single-picture-overlay').animate({opacity:0}, {'duration':1000, complete:function(){
                    $(this).css({
                        'background-image': '',
                        opacity: 1
                    });
                }});
                break;
            case 2:
                for(var i = 0; i < 4; i++) {
                    var key = '.answer.id-' + i + ' .four-images-overlay';
                    $(key).css('background-image', $('.answer.id-' + i).css('background-image'));
                    $('.answer.id-' + i).css({'background-image': 'url("/images/game/guess/' + this.currentQuestion.options[i] + '")'});
                    $(key).animate({opacity:0}, {'duration':1000, complete:function(){
                        $(this).css({
                            'background-image': '',
                            opacity: 1
                        });
                    }});
                }
                break;
        }
        setTimeout(function(){
            Guess.Game.restartTimer();
        },500);
        $('.to-delete').remove();
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

    this.ability = function(el) {
        el.off();
        var id = el.data('id');
        this.stopTimer();
        this.abilityButtonAnimation(el);

        url = '/GuessSeries/ability';
        Ajax.json(url, {
            data: 'id=' + id,
            callBack : function(data){Guess.Game.abilityResult(data)}
        });
    }

    this.sendAnswer = function(el) {
        this.stopTimer();
        var dSec =  this.checkTime();
        if (this.secondsLeft > this.currentQuestion.sec - dSec + 2) {
            this.secondsLeft = this.currentQuestion.sec - dSec;
        }
        $('.answer').off('click');
        if (!dSec) {
            return ;
        }
        var id = el.data('id');
        this.currentAnswer = id;

        url = '/GuessSeries/answer';
        Ajax.json(url, {
            data: 'id=' + id + '&seconds=' + this.secondsLeft,
            callBack : function(data){Guess.Game.result(data)}
        });
        this.animateAnswerSent();
    }

	this.result = function(data) {
        if (data.finish !== undefined) {
            this.stopGame();
            this.pointsAmount = data.points;
            this.correctAnswersNumber = data.correctAnswersNumber;
            this.correctAnswer = data.correctAnswer;
            this.userName = data.name;
            this.stats = data.stats;
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
            this.answerWasCorrectAnimations();
        }
	}
    this.abilityResult = function(data) {
        info(data);
        switch(data.id) {
            case 1:
                this.restartTimer();
                var wrong1 = data.wrong[0];
                var wrong2 = data.wrong[1];
                $('.answer.id-' + wrong1 + ', .answer.id-' + wrong2).off().animate({'opacity':0}, 300);
                break;
            case 2:
                this.currentQuestion = data.question;
                this.nextAnimationsEnded = true;
                this.pointsAnimationEnabled = false;
                this.prepareToStartTurn(data.question);
                break;
            case 3:
                this.currentQuestion = data.question;
                this.isSwitchImages = true;
                this.prepareToStartTurn(data.question);
                break;
        }

    }
    this.timeIsOut = function(data) {
        this.stopGame();
        this.pointsAmount = data.points;
        this.correctAnswer = data.correctAnswer;
        this.correctAnswersNumber = data.correctAnswersNumber;
        this.userName = data.name;
        this.stats = data.stats;
        this.showFalseAnswerAnimation();
    }
    this.stopGame = function() {
        this.gameFinished = true;
        $('.ability').each(function(){
            Guess.Game.abilityButtonAnimation($(this));
        });
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
        if (Guess.Game.nextImagesLoaded && Guess.Game.isSwitchImages) {
            Guess.Game.switchImagesAction();
        } else if (Guess.Game.nextImagesLoaded && Guess.Game.nextAnimationsEnded) {
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
        var correctAnswerEl = $('.answer.name.id-' + this.currentAnswer + ' .block');
        if (correctAnswerEl.length) {
            $('.answer .block').off();
            correctAnswerEl.css({'background-color':Guess.Game.color.yellow});
        }
        this.hideOtherAnswers(this.currentAnswer);
        setTimeout(function(){Guess.Game.answerAnimationEnded()}, 300);
    }
    this.hideOtherAnswers = function(otherFromThat) {
        for(var i = 0; i < 4; i++) {
            var answerEl = $('.answer.id-' + i);

            if (i !== otherFromThat) {
                if (answerEl.hasClass('name')) {
                    answerEl.find('.block').css({'background-color':Guess.Game.color.yellow});
                }
                answerEl.animate({
                    opacity:0
                }, 300);
            }
        }
    }

    this.answerWasCorrectAnimations = function() {
        var correctAnswerEl = $('.answer.name.id-' + this.currentAnswer + ' .block');
        if (correctAnswerEl.length) {
            $('.answer .block').stop();
            correctAnswerEl.animate({'background-color':Guess.Game.color.green, 'color':this.color.white}, 300);
        }
    }

    this.showQuestionResult = function() {
        $('.question').animate({opacity:0}, {duration:1100, complete:function(){Guess.Game.startTurn(false)}});
        if (this.pointsAnimationEnabled) {
            var k = this.resultK;
            var sec = this.resultSec;
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
                    $('.points-amount').html(Guess.Game.pointsAmount).css({'font-size':'36px', color:Guess.Game.color.yellow}).animate({'font-size':'20px', color:'#ffffff'}, 500);
                    $(this).hide();
                }});
        } else {
            this.pointsAnimationEnabled = true;
        }
    }

    this.abilityButtonAnimation = function(el) {
        el.animate({'border-radius': '80px', 'background-color':Guess.Game.color.yellow}, {'duration':1200, complete:function(){$(this).hide(100)}});
        el.delay(250).animate({'opacity': 0}, {'duration':1000, 'queue':false});
    }

    this.showFalseAnswerAnimation = function() {
        var duration = 1200;
        if (this.currentAnswer != 'none') {
            var currentAnswerEl = $('.answer.id-' + this.currentAnswer);
            currentAnswerEl.animate({'opacity':0.8}, duration);
            if (currentAnswerEl.hasClass('name')) {
                currentAnswerEl.find('.block').animate({'background-color':Guess.Game.color.red, 'color':this.color.white}, duration);
            } else {

            }
        } else {
            this.hideOtherAnswers(this.correctAnswer);
        }
        var correctAnswerEl = $('.answer.id-' + this.correctAnswer);

        correctAnswerEl.animate({opacity:1}, {'duration':duration});
        if (correctAnswerEl.hasClass('name')) {
            correctAnswerEl.find('.block').animate({'background-color':Guess.Game.color.green}, duration);
            $('.answer .block').off();
        }
        setTimeout(function(){Guess.Game.showEndModal()}, 2500);
    }

    this.showEndModal = function() {

        $('.turn-area, .col-md-10.timer').animate({opacity:0}, {duration:300, complete:function(){
            $('.turn-area, .col-md-10.timer').hide(500);
        }});//,
        setTimeout(function(){
            $('.game-area').removeClass('col-md-9').addClass('col-md-8');
            $('.sidebar-col').removeClass('col-md-3').addClass('col-md-4');
        }, 800);
        $('.points-container').delay(800).animate({'width':'100%'}, 300);
        //$('.points-amount').delay(900).animate({'color':'#ffffff'}, 600);
        $('.points').delay(1100).animate(
            {
                'height':'560px',
                'padding-top':'50px',
                'margin-top':'20px'
            }, {duration:600, complete:function(){
                $('.points').addClass('points-modal');
                Guess.Game.fillEndModal();
            }});
    }

    this.fillEndModal = function() {
        var answersText = 'You gave ' + this.correctAnswersNumber + ' correct answers';
        var template = $('#template-end-modal').html();
        Mustache.parse(template);
        var rendered = Mustache.render(template, {'data' : {'answers':this.correctAnswersNumber}, 'number': this.pointsAmount, 'userName' : this.userName});
        var obj = $(rendered);
        $('.points-modal').html('').append(obj);

        $('.points-amount .number').animate({'font-size':'40px'}, 800);
        $('.points-amount').animate({'font-size':'30px'}, 800);
        var queue = [
            {text:' points', el:$('.points-amount .right'), options:{'duration':500}},
            {text:answersText, el:$('.rest-stats-text'), options:{'duration':800}}
        ];
        if (this.userName) {
            queue.push({text:'Saved for name: ' + this.userName, el:$('.modal-user-name'), options:{'duration':800}})
        } else {
            Ajax.init();
        }
        $('.rest-stats-text').delay(1000).animate({'font-size':'30px'}, 800);
        pasteText('You earned', $('.points-amount .left'), {'duration':500, 'queue':queue});

        //facebook
        $('.facebook-placeholder').append($('.facebook-like-hidden :first-child'));

        var template = $('#template-sidebar-stats').html();
        Mustache.parse(template);
        var rendered = Mustache.render(template, {'stats' : this.stats});
        var obj = $(rendered);
        $('.sidebar-col').html('');
        $('.sidebar-col').append(obj);
        obj.fadeIn(400);
    }

    this.hideNameForm = function() {
        $('.end-modal').hide();
        pasteText('Results saved :)', $('.modal-user-name'), {'duration':500});
    }

    this.restartTimer = function()
    {
        $('.timer .progress-bar').css({'width' : '100%'});
        this.secondsLeft = this.currentQuestion.sec;
        $('.timer .seconds .text').html(this.secondsLeft);
        $('.timer .seconds')
            .css({'background-color': Guess.Game.color.green})
            .animate({'background-color':Guess.Game.color.red}, this.currentQuestion.sec * 1000);
        this.turnStartTime   = new Date();
        this.timerInterval = setInterval(function() { Guess.Game.timerTick() }, 1000);
    }
    this.timerTick = function() {
        this.secondsLeft--;
        var dSec = this.checkTime();

        var percent = 100 - dSec / (this.currentQuestion.sec / 100);
        $('.timer .progress-bar').css({'width' : percent + '%'});
        $('.timer .seconds .text').html(this.secondsLeft);
    }
    this.stopTimer = function() {
        $('.timer .seconds').stop();
        window.clearInterval(this.timerInterval);
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
        $('.answer .block').css({'background-color':Guess.Game.color.blue, 'color':Guess.Game.color.white});
        el.bounce();
        el.animate({'background-color':Guess.Game.color.yellow, 'color':Guess.Game.color.black}, {
            queue:false,
            duration:400
        });
    }
    this.answerButtonMouseOut = function(el) {
        el.stop();
        el.css({'background-color':Guess.Game.color.blue, 'color':Guess.Game.color.white});
    }

    this.answerImageMouseOver = function(el) {
        el.animate({'background-size': '103%'}, {
            queue:false,
            duration:400
        });
    }
    this.answerImageMouseOut = function(el) {
        el.stop();
        el.css({'background-size': '130%'});
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