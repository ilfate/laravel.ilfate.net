

@if ($game['result'] == \Tcg\Game::GAME_RESULT_WIN)

YOU WON

@endif

@if ($game['result'] == \Tcg\Game::GAME_RESULT_LOOSE)

YOU LOST

@endif

@if ($game['result'] == \Tcg\Game::GAME_RESULT_DRAW)

well... that was a draw

@endif