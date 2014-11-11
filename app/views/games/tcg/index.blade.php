@extends('layout.layoutTCG')

@section('content')

@include('games.tcg.game', array('game' => $game))

@stop



@section('sidebar')

<h3>Info</h3>
Turn player: <span class="playerTurnId">{{{$game['js']['playerTurnId']}}}</span> <br>
Turn number: {{{$game['js']['turnNumber']}}} <br>

<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/clear">Clear the game</a><br><br>
<a class="btn btn-primary" href="/tcg/action?action=skip">Skip movement</a>

<script>
	$(document).ready(function() {
        TCG.Game.init({{json_encode($game['js'])}});
		TCG.Game.renderFieldUnits({{json_encode($game['field']['map'])}});
		TCG.Game.renderHandCards({{json_encode($game['hand'])}});
        TCG.Game.processGame();


	});
</script>

@include('games.tcg.templates')

@stop