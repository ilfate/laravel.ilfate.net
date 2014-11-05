
<h3>Battle</h3>

@include('games.tcg.opponentHand', array('hand' => $game['opponentHand']))


@include('games.tcg.field', array('field' => $game['field'], 'cardFocus' => $game['card']))

@include('games.tcg.order', array('field' => $game['field'], 'cardFocus' => $game['card']))


@include('games.tcg.hand', array('hand' => $game['hand'], 'mode' => 'battle'))


@include('games.tcg.log', array('log' => $game['log']))