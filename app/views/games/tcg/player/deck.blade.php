@extends('layout.tcg.main')

@section('content')


    <a class="btn btn-primary" href="/tcg/changeDeck?deckId={{{$deck->id}}}">Deck settings</a><br>
    <input type="hidden" value="{{{$deck->id}}}" id="deckId">

	<h2>{{{$deck->name}}}</h2>
	<div class="row noselect">

		<div class="col-md-8">
			<h3>Your cards</h3>
			<div class="available-cards">
				@foreach($myCards as $card) 
					@include('games.tcg.cards.deckBuilderCard', array('card' => $card, 'hide' => $inDeck))
				@endforeach
			</div>
		</div>
		<div class="col-md-4">
			<h3>In Deck</h3>
			<div class="in-deck">
                @foreach($myCards as $card)
                    @include('games.tcg.cards.deckBuilderCard', array('card' => $card, 'mode' => 'empty', 'show' => $inDeck))
                @endforeach
			</div>
		</div>
	</div>
	
<script>
    $(document).ready(function() {
        deckBuilderPage();
    });
</script>

@stop

@section('sidebar')

	<h3>Info</h3>
	<a class="btn btn-primary" href="/tcg/me">Back to my page</a>


	<div class="info-zone">
    	@foreach($myCards as $card) 
			@include('games.tcg.cards.nonGameCard', array('card' => $card, 'mode' => 'info'))
		@endforeach
	</div>

    <a class="btn btn-success save-deck-button" href="#">Save</a>

@stop