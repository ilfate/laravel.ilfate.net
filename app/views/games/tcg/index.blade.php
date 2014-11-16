@extends('layout.layoutTCG')

@section('content')

@include('games.tcg.game', array('game' => $game))

@stop


@section('right-content')

@include('games.tcg.order', array('field' => $game['field'], 'cardFocus' => $game['card']))

@stop

@section('sidebar')

<h3>Info</h3>
Turn player: <span class="playerTurnId">{{{$game['js']['playerTurnId']}}}</span> <br>
Turn number: {{{$game['js']['turnNumber']}}} <br>

<h3>Actions</h3>
<a class="btn btn-primary" href="/tcg/clear">Clear And create Battle</a><br><br>
<a class="btn btn-primary" href="/tcg/clear?debug=true">Clear And create Debug Game</a><br><br>
<a class="btn btn-primary" href="/tcg/clear?debug=true&bot=true">Clear And create Debug Game with Bot</a><br><br>
<a class="btn btn-primary" href="/tcg/clear?situation=true">Create Situation</a><br><br>
<a class="btn btn-primary" href="/tcg/clear?situation=true&bot=true">Create Situation with Bot</a><br><br>


<script>
	$(document).ready(function() {
        TCG.Game.init({{json_encode($game['js'])}});
		TCG.Game.renderFieldUnits({{json_encode($game['field']['map'])}});
		TCG.Game.renderHandCards({{json_encode($game['hand'])}});
        TCG.Game.processGame();


	});
</script>

<script>
    var conn = new ab.Session('ws://localhost:8080',
        function() {
            conn.subscribe('{{$game['js']['subscriptionKey']}}', function(topic, data) {
                // This is where you would add the new article to the DOM (beyond the scope of this tutorial)
                //console.log('New article published to category "' + topic + '" : ' + data.title);
                info(data);
                TCG.Game.processLog(data);
            });
        },
        function() {
            console.warn('WebSocket connection closed');
        },
        {'skipSubprotocolCheck': true}
    );
</script>


@include('games.tcg.templates')

@stop