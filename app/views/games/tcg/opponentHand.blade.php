
<div class="hand player_{{$hand['playerId']}}">
	@for ($i = 0; $i < $hand['size']; $i ++)
		@include('games.tcg.cards.backCard')
	@endfor
</div>