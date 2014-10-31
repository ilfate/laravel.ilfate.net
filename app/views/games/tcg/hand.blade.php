
<div class="hand">
	@foreach ($hand as $card)
		@include('games.tcg.cards.handCard', array('card' => $card))
	@endforeach
</div>
