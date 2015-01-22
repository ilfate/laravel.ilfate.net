

<a id="start-game" class="btn btn-primary btn-lg btn-block"> Start </a>

<div class="game-container" style="display:none">

	<div class="turn-area"></div>
</div>

<div class="row">
    <div class="col-md-12 timer">
        <div class="seconds"></div>
    </div>
</div>

<script>
	$(document).ready(function() {
        Guess.Game.init({{$firstQuestion}});
	});
</script>