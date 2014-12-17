@extends('layout.tcg.main')

@section('content')


    <a class="btn btn-primary" href="/tcg/changeDeck?deckId={{{$deck->id}}}">Deck settings</a><br><br>

	<h2>{{{$deck->name}}}</h2>
	<div class="row">

		<div class="col-md-8">
			<h3>Your cards</h3>
			<div class="available-cards">
				@foreach($myCards as $card) 
					@include('games.tcg.cards.nonGameCardInList', array('card' => $card, 'selected' => true))
				@endforeach
			</div>
		</div>
		<div class="col-md-4">
			<h3>In Deck</h3>
			<div class="in-deck">
				
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

@stop