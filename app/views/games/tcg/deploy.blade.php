
<h3>Units Deploy</h3>

@include('games.tcg.opponentHand', array('hand' => $game['opponentHand']))


@include('games.tcg.field', array('field' => $game['field']))


@include('games.tcg.hand', array('hand' => $game['hand'] ,'mode' => 'deploy'))