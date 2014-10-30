
<div class="hand">
	@foreach ($hand as $card)
		@include('games.tcg.handCard', array('card' => $card))
	@endforeach
</div>
