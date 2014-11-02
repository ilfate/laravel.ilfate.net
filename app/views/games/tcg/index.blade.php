@extends('layout.layout')

@section('additional_css')
<link href="css/tcg/main.css" rel="stylesheet">
<link href="font-awesome-4.2.0/css/font-awesome.min.css" rel="stylesheet">
@stop



@section('additional_js')
<script type="text/javascript" src="/js/tcg/tcg.js"></script>

@stop


@section('content')

@include('games.tcg.' . $game['template'], array('game' => $game))

@stop



@section('sidebar')

<h3>Info</h3>
Turn player: {{{$game['turn']}}} <br>
Turn number: {{{$game['turnNumber']}}} <br>

<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/clear">Clear the game</a>

<script>
	$(document).ready(function() {
		TCG.Game.init({{json_encode($game['js'])}});
		
	});
</script>

@stop