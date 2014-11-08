@extends('layout.layoutTCG')

@section('content')

@include('games.tcg.' . $game['template'], array('game' => $game))

@stop



@section('sidebar')

<h3>Info</h3>
Turn player: {{{$game['turn']}}} <br>
Turn number: {{{$game['turnNumber']}}} <br>

<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/clear">Clear the game</a><br><br>
<a class="btn btn-primary" href="/tcg/action?action=skip">Skip movement</a>

<script>
	$(document).ready(function() {
		TCG.Game.init({{json_encode($game['js'])}});
		
	});
</script>

@stop