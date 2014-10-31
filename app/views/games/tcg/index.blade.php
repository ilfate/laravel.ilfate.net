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
Turn: {{{$game['turn']}}}

<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/clear">Clear the game</a>

<h3>Deploy</h3>
<form method="get" action="/tcg/action">
	<input name="action" type="hidden" value="deploy" />
	<table>
		<tr>
			<td><label>card Id</label></td>
			<td><input name="cardId" type="text" /></td>
		</tr>
		<tr>
			<td><label>x</label></td>
			<td><input name="x" type="text" /></td>
		</tr>
		<tr>
			<td><label>y</label></td>
			<td><input name="y" type="text" /></td>
		<tr>
	</table>
	<input type="submit" />
</form>

<script>
	$(document).ready(function() {
		TCG.Game.init({{json_encode($game['js'])}});
		
	});
</script>

@stop