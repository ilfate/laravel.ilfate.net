

<div class="order">
@foreach($field['order'] as $card)

            @include('games.tcg.cards.orderCard', array('card' => $card, 'isFocus' => $cardFocus == $card['id']))

@endforeach

</div>