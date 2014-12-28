/**
 * Created by ilfate on 12/10/14.
 */


function initFormCard()
{
    $(".non-game-card").on('click', function(){
        $(this).find('input.hidden').prop("checked", true);
        $(".non-game-card.focus").removeClass('focus');
        $(this).addClass('focus');
    });
}

/****   /tcg/deck/id - page *****/

function deckBuilderPage () {
	$('.deck_builder-card-container').on({
		mouseenter: function(){
			var cardId = $(this).data('cardid');
	        //var id = unit.data('id');
	        $('.info-zone .card-container').hide();
	        $('.info-zone .info-card.id-' + cardId).parent().show();
		},
		mouseleave: function(){
			$('.info-zone .card-container').hide();
		},
		click: function() { togglePositionDeckBuilder($(this)) }
	});

    $('.save-deck-button').on({
        click: function() {
            var cards = [];
            //var cardsEl = $('.in-deck .non-game-card-in-list');
            $('.in-deck .non-game-card-in-list').each(function() {
                cards.push($(this).data('id'));
            });
            info(cards);
                //cards.push(cardsEl[i].data('id'));

            var url = '/tcg/saveDeck/' + $('#deckId').val();
            Ajax.json(url, {
                //params : '__csrf=' + Ajax.getCSRF(),
                    data: 'cards=' + cards,
//                        '&_token=' + $('#laravel-token').val()
                callBack : function(data){ Messages.createMessage('Saved') }
            });
        }
    });
}

function togglePositionDeckBuilder (el) {
	if (el.parent().hasClass('in-deck')) {
		// this one is in deck
        var moveEl = el.children().eq(0);
		$('.available-cards .card-id-' + el.data('cardid')).append(moveEl);
	} else {
        var moveEl = el.children().eq(0);
		$('.in-deck .card-id-' + el.data('cardid')).append(moveEl);
	}
}

function inQueuePage() {
    setInterval(function(){
        checkQueue();
    }, 5000)
}
function checkQueue() {
    Ajax.json('/tcg/checkQueue', {
        callBack : function(data){ checkQueueResponce(data) }
    });
}

function checkQueueResponce(data) {
    if (data.error !== undefined && data.error == 'not_in_queue') {
        location.reload();
    }
}

function Messages () {
    this.messages = [];
    this.messageIsShown = false;

    this.addMessage = function(message, type) {
        this.messages.push({'message' : message, 'type' : type});
        if (!this.messageIsShown) {
            this.showMessages();
        }
    }

    this.showMessages = function() {
        if (this.messages.length > 0) {
            var message = this.messages[0];
            this.createMessage(message.message, message.type)
            this.messages = this.messages.slice(1);
            this.messageIsShown = true;
        } else {
            this.messageIsShown = false;
        }
    }

    this.createMessage = function(text, type) {
        var template = $('#template-message').html();

        Mustache.parse(template);   // optional, speeds up future uses
        var rendered = Mustache.render(template, {text : text, type : type});
        info('Message:');
        info(text);
        var obj = $(rendered);

        $('#message-container').append(obj);

        obj.animate({'top' : 200} , {duration:2000});
        obj.delay(2000).animate({'opacity' : 0} , {duration:1000, 'complete': function () {
            $(this).css('opacity', 1).remove();
            Messages.showMessages();
        }});

    }
}
Messages = new Messages();