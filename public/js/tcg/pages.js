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
	info($('.non-game-card-in-list'));
	$('.non-game-card-in-list').on({
		mouseenter: function(){
			var cardId = $(this).data('id');
	        //var id = unit.data('id');
	        $('.info-zone .card-container').hide();
	        $('.info-zone .info-card.id-' + cardId).parent().show();
		},
		mouseleave: function(){
			$('.info-zone .card-container').hide();
		},
		click: function() { togglePositionDeckBuilder($(this)) }
	});
}

function togglePositionDeckBuilder (el) {
	if (el.parent().parent().hasClass('in-deck')) {
		// this one is in deck
		$('.available-cards').append(el.parent());
	} else {
		$('.in-deck').append(el.parent());
	}
}