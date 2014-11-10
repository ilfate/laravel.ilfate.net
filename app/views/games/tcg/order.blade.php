

<div class="order">
	@if(false)
@foreach($field['order'] as $card)

            @include('games.tcg.cards.orderCard', array('card' => $card, 'isFocus' => $cardFocus == $card['id']))

@endforeach
@endif

</div>