
<h3>Battle</h3>

@include('games.tcg.opponentHand', array('hand' => $game['opponentHand']))


@include('games.tcg.field', array('field' => $game['field']))

@include('games.tcg.hand', array('hand' => $game['hand'], 'mode' => 'battle', 'playerId' => $game['js']['currentPlayerId']))


@include('games.tcg.log', array('log' => $game['log']))