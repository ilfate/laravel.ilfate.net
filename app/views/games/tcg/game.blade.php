
@include('games.tcg.opponentHand', array('hand' => $game['opponentHand']))


@include('games.tcg.field', array('field' => $game['field']))

@include('games.tcg.hand', array('hand' => $game['hand'], 'mode' => 'battle', 'playerId' => $game['js']['currentPlayerId']))

<div class="tcg-footer">
    <div class="authors"></div>
</div>
